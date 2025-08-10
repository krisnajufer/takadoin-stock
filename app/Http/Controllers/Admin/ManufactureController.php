<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bom;
use App\Models\ItemStock;
use App\Models\Manufacture;
use App\Models\ManufactureItem;
use App\Models\StockLedgerEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class ManufactureController extends Controller
{
    public function index()
    {
        return view('admin.pages.manufacture.index');
    }

    public function create()
    {
        $result["action"] = "store";
        return view('admin.pages.manufacture.form', $result);
    }

    public function get_data(Request $request)
    {
        $query = Manufacture::query()
            ->selectRaw('id, DATE_FORMAT(posting_date, "%d-%m-%Y") AS posting_date');

        if ($request->filled('id')) {
            $query->where('id', 'like', "%{$request->id}%");
        }


        return DataTables::of($query)->toJson();
    }

    public function get_data_bom(Request $request)
    {
        $query = Bom::query()
            ->selectRaw('items.id, items.name as material, item_stocks.actual_qty as current_qty, bom_materials.qty * ' . $request->qty . ' AS needed_qty')
            ->join('bom_materials', 'boms.id', '=', 'bom_materials.bom_id')
            ->join('items', 'bom_materials.item_id', '=', 'items.id')
            ->join('item_stocks', 'items.id', '=', 'item_stocks.item_id')
            ->where('boms.item_id', $request->bouquet)->get();

        return $query;
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $mnf = new Manufacture();
            $mnf->id = Manufacture::get_new_code($request->is_material);
            $mnf->posting_date = Carbon::createFromFormat('d/m/Y H:i', $request->posting_date)->format('Y-m-d');
            $mnf->posting_time = Carbon::createFromFormat('d/m/Y H:i', $request->posting_date)->format('H:i');
            $mnf->save();

            $check = $this->store_items($request, $mnf);
            if ($check) {
                return response()->json($check, 200);
            }
            DB::commit();
            return response()->json("success add new data item", 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json($ex->getMessage(), 500);
        }
    }

    function store_items($request, $mnf)
    {
        foreach (json_decode($request->mnf_items) as $key => $value) {
            $bom = Bom::where('item_id', $value->bouquet)->first();
            $requestMock = new \Illuminate\Http\Request();
            $requestMock->merge([
                'qty' => $value->qty,
                'bouquet' => $value->bouquet,
            ]);

            $mnf_item = new ManufactureItem();
            $mnf_item->manufacture_id = $mnf->id;
            $mnf_item->item_id = $value->bouquet;
            $mnf_item->bom_id = $bom->id;
            $mnf_item->qty = $value->qty;
            $mnf_item->save();

            $data_bom = $this->get_data_bom($requestMock);
            $throw = [];
            foreach ($data_bom as $key => $row) {
                $result = $this->validate_material($mnf, $row);
                if ($result) {
                    array_push($throw, $result);
                }
            }

            if ($throw) {
                return $throw;
            }
            foreach ($data_bom as $key => $row) {
                $this->make_sle($mnf, $row);
            }
        }
    }

    function make_sle($mnf, $data_bom)
    {
        // $item = ItemStock::where('item_id', $data_bom->id)->first();
        $actual_qty_before = $this->calculate_before_posting_date($data_bom->id, $mnf->posting_date, $mnf->posting_time, $mnf->created_at);
        $sle = new StockLedgerEntry();
        $sle->id = StockLedgerEntry::get_new_code();
        $sle->transaction_type = 'Manufacture';
        $sle->transaction_id = $mnf->id;
        $sle->item_id = $data_bom->id;
        $sle->posting_date = $mnf->posting_date;
        $sle->posting_time = $mnf->posting_time;
        $sle->qty_change = $data_bom->needed_qty *-1;
        $sle->qty_after_transaction = $actual_qty_before + $sle->qty_change;
        $sle->save();

        $actual_qty = $this->actual_qty($data_bom->id, $mnf->posting_date);
        ItemStock::where('item_id', $data_bom->id)->update(['actual_qty' => $actual_qty]);
        $this->calculate_future_sle($sle);
    }

    function calculate_before_posting_date($item_id, $posting_date, $posting_time, $created_at)
    {
        $result = StockLedgerEntry::where('item_id', $item_id)
                    ->where(function($query) use ($posting_date, $posting_time, $created_at) {
                        $query->where('posting_date', '<', $posting_date)
                            ->orWhere(function($q) use ($posting_date, $posting_time, $created_at) {
                                $q->where('posting_date', $posting_date)
                                    ->where('posting_time', '<', $posting_time);
                            })
                            ->orWhere(function($q) use ($posting_date, $posting_time, $created_at) {
                                $q->where('posting_date', $posting_date)
                                    ->where('posting_time', $posting_time)
                                    ->where('created_at', '<', $created_at);
                            });
                    })
                    ->sum('qty_change');
        // dd($result);
        return $result;
    }
    function calculate_after_posting_date($item_id, $posting_date, $posting_time, $created_at)
    {
        $result = StockLedgerEntry::where('item_id', $item_id)
                    ->where(function($query) use ($posting_date, $posting_time, $created_at) {
                        $query->where('posting_date', '>', $posting_date)
                            ->orWhere(function($q) use ($posting_date, $posting_time, $created_at) {
                                $q->where('posting_date', $posting_date)
                                    ->where('posting_time', '>', $posting_time);
                            })
                            ->orWhere(function($q) use ($posting_date, $posting_time, $created_at) {
                                $q->where('posting_date', $posting_date)
                                    ->where('posting_time', $posting_time)
                                    ->where('created_at', '>', $created_at);
                            });
                    })
                    ->sum('qty_change');
        return $result;
    }

    function actual_qty($item_id)
    {
        $result = StockLedgerEntry::where('item_id', $item_id)
            ->orderBy('posting_date', 'asc')
            ->orderBy('posting_time', 'asc')
            ->orderBy('created_at', 'asc')
            ->sum('qty_change');
        return $result;
    }

    function validate_material($mnf, $bom){
        $actual_qty_before = $this->calculate_before_posting_date($bom->id, $mnf->posting_date, $mnf->posting_time, $mnf->created_at);
        $actual_qty_after = $this->calculate_after_posting_date($bom->id, $mnf->posting_date, $mnf->posting_time, $mnf->created_at);

        // dd($actual_qty_after, $actual_qty_before);
        if ($actual_qty_before < $bom->needed_qty) {
            // return "<li>Stok Material <b>".$bom->material."</b> di tanggal <b>".$mnf->posting_date."</b> hanya ".$actual_qty_before.", stok yang dibutuhkan ".$bom->needed_qty."</li>";
            return "<li>Stok material <b>{$bom->material}</b> pada tanggal <b>{$mnf->posting_date}</b> hanya tersedia <b>{$actual_qty_before}</b>, sedangkan jumlah yang dibutuhkan adalah <b>{$bom->needed_qty}</b>. Harap lakukan penyesuaian stok terlebih dahulu.</li>";

        }

        $after_calculate_qty = $actual_qty_before - $bom->needed_qty + $actual_qty_after;
        if ($after_calculate_qty < 0) {
            // return "<li>Stok Material <b>".$bom->material."</b> di tanggal <b>".$mnf->posting_date."</b> hanya ".$bom->current_qty.", stok yang dibutuhkan ".$bom->needed_qty." dan menjadi negatif yaitu ".$after_calculate_qty."</li>";
            return "<li>Stok material <b>{$bom->material}</b> pada tanggal <b>{$mnf->posting_date}</b> akan menjadi negatif setelah transaksi ini. Jumlah tersedia sebelum: <b>{$actual_qty_before}</b>, dikurangi kebutuhan: <b>{$bom->needed_qty}</b>, ditambah penambahan setelah: <b>{$actual_qty_after}</b>, menghasilkan sisa: <b>{$after_calculate_qty}</b>. Mohon periksa kembali pergerakan stok.</li>";
        }
    }

    function calculate_future_sle($sle){
        $posting_date = $sle->posting_date;
        $posting_time = $sle->posting_time;
        $created_at = $sle->created_at;
        $qty_after_transaction = $sle->qty_after_transaction;
        $result = StockLedgerEntry::where('item_id', $sle->item_id)
                    ->where(function($query) use ($posting_date, $posting_time, $created_at) {
                        $query->where('posting_date', '>', $posting_date)
                            ->orWhere(function($q) use ($posting_date, $posting_time, $created_at) {
                                $q->where('posting_date', $posting_date)
                                    ->where('posting_time', '>', $posting_time);
                            })
                            ->orWhere(function($q) use ($posting_date, $posting_time, $created_at) {
                                $q->where('posting_date', $posting_date)
                                    ->where('posting_time', $posting_time)
                                    ->where('created_at', '>', $created_at);
                            });
                    })
                    ->orderBy('posting_date', 'ASC')
                    ->orderBy('posting_time', 'ASC')
                    ->orderBy('created_at', 'ASC')
                    ->get();
        
        foreach ($result as $key => $val) {
            $qty_after_transaction += $val->qty_change;
            StockLedgerEntry::where('id', $val->id)->update(['qty_after_transaction' => $qty_after_transaction]);
        }
    }
}
