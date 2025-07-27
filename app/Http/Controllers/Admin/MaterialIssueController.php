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
        $query = MaterialIssue::query()
            ->selectRaw('material_issues.id, DATE_FORMAT(material_issues.posting_date, "%d-%m-%Y") AS posting_date, material_issues.issue_type');

        if ($request->filled('id')) {
            $query->where('material_issues.id', 'like', "%{$request->id}%");
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

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $mtr_issue = new MaterialIssue();
            $mtr_issue->id = MaterialIssue::get_new_code($request->is_material);
            $mtr_issue->posting_date = Carbon::createFromFormat('d/m/Y', $request->posting_date)->format('Y-m-d');
            $mtr_issue->issue_type = $request->issue_type;
            $mtr_issue->save();

            $this->store_items($request, $mtr_issue);

            DB::commit();
            return response()->json("success add new data item", 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json($ex->getMessage(), 500);
        }
    }

    function store_items($request, $mtr_issue)
    {
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
        $item = ItemStock::where('item_id', $issue_item->item_id)->first();
        $sle = new StockLedgerEntry();
        $sle->id = StockLedgerEntry::get_new_code();
        $sle->transaction_type = 'Material Issue';
        $sle->transaction_id = $mtr_issue->id;
        $sle->item_id = $issue_item->item_id;
        $sle->posting_date = $mtr_issue->posting_date;
        $sle->qty_change = $issue_item->qty *-1;
        $sle->qty_after_transaction = $item->actual_qty - $issue_item->qty;
        $sle->save();

        $actual_qty = $this->calculate_qty($issue_item->item_id)->actual_qty;
        ItemStock::where('item_id', $issue_item->item_id)->update(['actual_qty' => $actual_qty, 'issue_qty' => $item->issue_qty + $issue_item->qty]);
    }

    function calculate_qty($item_id)
    {
        $result = StockLedgerEntry::selectRaw('item_id, COALESCE(SUM(qty_change), 0) AS actual_qty')
            ->where('item_id', $item_id)
            ->groupBy('item_id')->first();

        return $result;
    }
}
