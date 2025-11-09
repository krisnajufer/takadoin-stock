<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemStock;
use App\Models\MaterialIssue;
use App\Models\MaterialIssueItem;
use App\Models\StockLedgerEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class MaterialIssueController extends Controller
{
    public function index()
    {
        return view('admin.pages.material-issue.index');
    }

    public function get_data(Request $request)
    {
        $query = MaterialIssueItem::query()
            ->selectRaw('material_issues.id, material_issue_items.item_id, items.name AS item_name, DATE_FORMAT(material_issues.posting_date, "%d-%m-%Y") AS posting_date, material_issues.issue_type')
            ->join('material_issues', 'material_issues.id', '=', 'material_issue_items.material_issue_id')
            ->join('items', 'items.id', '=', 'material_issue_items.item_id');

        if ($request->filled('item_name')) {
            $query->where('items.name', 'like', "%{$request->item_name}%");
        }
        if ($request->filled('issue_type')) {
            $query->where('material_issues.issue_type', '=', "{$request->issue_type}");
        }

        return DataTables::of($query)->toJson();
    }

    public function create()
    {
        $result["action"] = "store";
        return view('admin.pages.material-issue.form', $result);
    }

    public function edit(string $id)
    {
        $id = str_replace("-", "/", $id);
        $result['mtr_issue'] = MaterialIssue::selectRaw('material_issues.id, CONCAT(DATE_FORMAT(material_issues.posting_date, "%d/%m/%Y")," ", material_issues.posting_time) AS posting_date, material_issues.issue_type')->where('material_issues.id', $id)->first();
        $result["action"] = "update";
        $result['issue_items'] = MaterialIssueItem::join('material_issues', 'material_issues.id', '=', 'material_issue_items.material_issue_id')
            ->join('items', 'items.id', '=', 'material_issue_items.item_id')
            ->selectRaw("material_issue_items.item_id, items.name AS item_name, material_issue_items.qty, material_issue_items.price, material_issue_items.amount")
            ->where('material_issues.id', $id)->get();

        return view('admin.pages.material-issue.form', $result);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $mtr_issue = new MaterialIssue();
            $mtr_issue->id = MaterialIssue::get_new_code();
            $mtr_issue->posting_date = Carbon::createFromFormat('d/m/Y H:i', $request->posting_date)->format('Y-m-d');
            $mtr_issue->posting_time = Carbon::createFromFormat('d/m/Y H:i', $request->posting_date)->format('H:i');
            $mtr_issue->issue_type = $request->issue_type;
            $mtr_issue->save();

            $check = $this->store_items($request, $mtr_issue);
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

    function store_items($request, $mtr_issue)
    {
        $throw = [];
        foreach (json_decode($request->issue_items) as $key => $value) {
            $result = $this->validate_material($mtr_issue, $value);
            if ($result) {
                array_push($throw, $result);
            }
        }

        if ($throw) {
            return $throw;
        }
        // dd(json_decode($request->issue_items));
        foreach (json_decode($request->issue_items) as $key => $value) {
            $issue_item = new MaterialIssueItem();
            $issue_item->material_issue_id = $mtr_issue->id;
            $issue_item->item_id = $value->material;
            $issue_item->qty = $value->qty;
            $issue_item->price = $value->price;
            $issue_item->amount = $value->amount;
            $issue_item->save();

            $this->make_sle($mtr_issue, $issue_item);
        }
    }

    function make_sle($mtr_issue, $issue_item)
    {
        $actual_qty_before = $this->calculate_before_posting_date($issue_item->item_id, $mtr_issue->posting_date, $mtr_issue->posting_time, $mtr_issue->created_at);

        $sle = new StockLedgerEntry();
        $sle->id = StockLedgerEntry::get_new_code();
        $sle->transaction_type = 'Material Issue';
        $sle->transaction_id = $mtr_issue->id;
        $sle->item_id = $issue_item->item_id;
        $sle->posting_date = $mtr_issue->posting_date;
        $sle->posting_time = $mtr_issue->posting_time;
        $sle->qty_change = $issue_item->qty *-1;
        $sle->qty_after_transaction = $actual_qty_before - $issue_item->qty;
        $sle->save();

        $actual_qty = $this->actual_qty($issue_item->item_id);
        $item = ItemStock::where('item_id', $issue_item->item_id)->first();
        ItemStock::where('item_id', $issue_item->item_id)->update(['actual_qty' => $actual_qty, 'issue_qty' => $item->issue_qty + $issue_item->qty]);
        $this->calculate_future_sle($sle);
    }

    function actual_qty($item_id)
    {
        $result = StockLedgerEntry::where('item_id', $item_id)
            ->orderBy('posting_date', 'asc')
            ->orderBy('posting_time', 'asc')
            ->sum('qty_change');
        return $result;
    }

    function calculate_before_posting_date($item_id, $posting_date, $posting_time, $created_at)
    {
        $posting_timestamp = $posting_date . " " . $posting_time;

        $result = StockLedgerEntry::whereRaw(
            'item_id = ? AND TIMESTAMP(posting_date, posting_time) <= ? AND created_at < ?',
            [$item_id, $posting_timestamp, $created_at]
        )->sum('qty_change');
        // dd($result);
        return $result;
    }
    function calculate_after_posting_date($item_id, $posting_date, $posting_time, $created_at)
    {
        $posting_timestamp = $posting_date . " " . $posting_time;

        $result = StockLedgerEntry::whereRaw(
            'item_id = ? AND TIMESTAMP(posting_date, posting_time) >= ? AND created_at > ?',
            [$item_id, $posting_timestamp, $created_at]
        )->sum('qty_change');

        return $result;
    }

    function calculate_future_sle($sle){
        $posting_timestamp = $sle->posting_date ." ". $sle->posting_time;
        $created_at = $sle->created_at;
        $qty_after_transaction = $sle->qty_after_transaction;
        $result = StockLedgerEntry::whereRaw(
                        'item_id = ? AND TIMESTAMP(posting_date, posting_time) >= ? AND created_at > ?',
                        [$sle->item_id, $posting_timestamp, $created_at]
                    )
                    ->orderBy('posting_date', 'ASC')
                    ->orderBy('posting_time', 'ASC')
                    ->orderBy('created_at', 'ASC')
                    ->get();
        
        foreach ($result as $key => $val) {
            $qty_after_transaction += $val->qty_change;
            StockLedgerEntry::where('id', $val->id)->update(['qty_after_transaction' => $qty_after_transaction]);
        }
    }

    function validate_material($mtr_issue, $issue_item){
        $actual_qty_before = $this->calculate_before_posting_date($issue_item->material, $mtr_issue->posting_date, $mtr_issue->posting_time, $mtr_issue->created_at);
        $actual_qty_after = $this->calculate_after_posting_date($issue_item->material, $mtr_issue->posting_date, $mtr_issue->posting_time, $mtr_issue->created_at);

        // dd($actual_qty_after, $actual_qty_before);
        if ($actual_qty_before < $issue_item->qty) {
            // return "<li>Stok Material <b>".$issue_item->material."</b> di tanggal <b>".$mtr_issue->posting_date."</b> hanya ".$actual_qty_before.", stok yang dibutuhkan ".$issue_item->qty."</li>";
            return "<li>Stok material <b>{$issue_item->material}</b> pada tanggal <b>{$mtr_issue->posting_date}</b> hanya tersedia <b>{$actual_qty_before}</b>, sedangkan jumlah yang dibutuhkan adalah <b>{$issue_item->qty}</b>. Harap lakukan penyesuaian stok terlebih dahulu.</li>";

        }

        $after_calculate_qty = $actual_qty_before - $issue_item->qty + $actual_qty_after;
        if ($after_calculate_qty < 0) {
            // return "<li>Stok Material <b>".$issue_item->material."</b> di tanggal <b>".$mtr_issue->posting_date."</b> hanya ".$issue_item->current_qty.", stok yang dibutuhkan ".$issue_item->qty." dan menjadi negatif yaitu ".$after_calculate_qty."</li>";
            return "<li>Stok material <b>{$issue_item->material}</b> pada tanggal <b>{$mtr_issue->posting_date}</b> akan menjadi negatif setelah transaksi ini. Jumlah tersedia sebelum: <b>{$actual_qty_before}</b>, dikurangi kebutuhan: <b>{$issue_item->qty}</b>, ditambah penambahan setelah: <b>{$actual_qty_after}</b>, menghasilkan sisa: <b>{$after_calculate_qty}</b>. Mohon periksa kembali pergerakan stok.</li>";
        }
    }
}
