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
            $mnf->posting_date = Carbon::createFromFormat('d/m/Y', $request->posting_date)->format('Y-m-d');
            $mnf->save();

            $this->store_items($request, $mnf);

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
            foreach ($data_bom as $key => $row) {
                $this->make_sle($mnf, $row);
            }
        }
    }

    function make_sle($mnf, $data_bom)
    {
        $item = ItemStock::where('item_id', $data_bom->id)->first();
        $sle = new StockLedgerEntry();
        $sle->id = StockLedgerEntry::get_new_code();
        $sle->transaction_type = 'Manufacture';
        $sle->transaction_id = $mnf->id;
        $sle->item_id = $data_bom->id;
        $sle->posting_date = $mnf->posting_date;
        $sle->qty_change = $data_bom->needed_qty *-1;
        $sle->qty_after_transaction = $item->actual_qty - $data_bom->needed_qty;
        $sle->save();

        $actual_qty = $this->calculate_qty($data_bom->id)->actual_qty;
        ItemStock::where('item_id', $data_bom->id)->update(['actual_qty' => $actual_qty]);
    }

    function calculate_qty($item_id)
    {
        $result = StockLedgerEntry::selectRaw('item_id, COALESCE(SUM(qty_change), 0) AS actual_qty')
            ->where('item_id', $item_id)
            ->groupBy('item_id')->first();

        return $result;
    }
}
