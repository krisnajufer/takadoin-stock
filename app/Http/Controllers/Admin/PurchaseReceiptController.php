<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\StockLedgerEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PurchaseReceiptController extends Controller
{
    public function index()
    {
        return view('admin.pages.purchase-receipt.index');
    }

    public function get_data(Request $request)
    {
        $query = PurchaseOrder::query()
            ->selectRaw('purchase_orders.id, DATE_FORMAT(purchase_orders.posting_date, "%d-%m-%Y") AS posting_date, purchase_orders.status, suppliers.name AS supplier_name, suppliers.id AS suuplier_id')
            ->join('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')
            ->where('purchase_orders.status', 'Dipesan');

        if ($request->filled('id')) {
            $query->where('purchase_orders.id', 'like', "%{$request->id}%");
        }
        if ($request->filled('supplier')) {
            $query->where('suppliers.id', 'like', "%{$request->supplier}%");
        }

        return DataTables::of($query)->toJson();
    }

    public function edit(string $id)
    {
        $id = str_replace("-", "/", $id);
        $result['po'] = PurchaseOrder::selectRaw('purchase_orders.id, purchase_orders.posting_date, purchase_orders.status, suppliers.name AS supplier_name, suppliers.id AS suuplier_id')
            ->join('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')->where('purchase_orders.id', $id)->first();
        $result["action"] = "update";
        $result['po_items'] = PurchaseOrderItem::join('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_items.purchase_order_id')
            ->join('items', 'items.id', '=', 'purchase_order_items.item_id')
            ->selectRaw("purchase_order_items.item_id, items.name AS item_name, purchase_order_items.qty, purchase_order_items.price, purchase_order_items.amount")
            ->where('purchase_orders.id', $id)->get();

        return view('admin.pages.purchase-receipt.form', $result);
    }

    public function received(Request $request)
    {
        DB::beginTransaction();
        try {
            $ids = $request->json()->all();
            $posting_date = Carbon::createFromFormat('d/m/Y', $ids['posting_date'])->format('Y-m-d');
            PurchaseOrder::whereIn('id', $ids['data'])->update(['status' => 'Diterima', 'received_at' => $posting_date]);
            $this->make_sle($ids['data'], $posting_date);
            DB::commit();
            return response()->json("success received data", 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json($ex->getMessage(), 500);
        }
    }

    function make_sle($data_id, $posting_date)
    {
        foreach ($data_id as $key => $val) {
            $po_items = PurchaseOrderItem::where('purchase_order_id', $val)->get();
            foreach ($po_items as $key => $detail) {
                $item = ItemStock::where('item_id', $detail->item_id)->first();
                $sle = new StockLedgerEntry();
                $sle->id = StockLedgerEntry::get_new_code();
                $sle->transaction_type = 'Purchase Order';
                $sle->transaction_id = $val;
                $sle->item_id = $detail->item_id;
                $sle->posting_date = $posting_date;
                $sle->qty_change = $detail->qty;
                $sle->qty_after_transaction = $item->actual_qty + $detail->qty;
                $sle->save();

                $actual_qty = $this->calculate_qty($detail->item_id)->actual_qty;

                $item_stock = ItemStock::where('item_id', $detail->item_id)->first();
                ItemStock::where('item_id', $detail->item_id)->update(['purchase_qty' => $item_stock->purchase_qty - $detail->qty, 'actual_qty' => $actual_qty]);
            }
        }
    }

    function calculate_qty($item_id)
    {
        $result = StockLedgerEntry::selectRaw('item_id, SUM(qty_change) AS actual_qty')
            ->where('item_id', $item_id)
            ->groupBy('item_id')->first();

        return $result;
    }
}
