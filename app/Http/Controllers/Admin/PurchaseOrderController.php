<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemStock;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Carbon;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.pages.purchase-order.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $result["action"] = "store";
        return view('admin.pages.purchase-order.form', $result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $po = new PurchaseOrder();
            $po->id = PurchaseOrder::get_new_code($request->is_material);
            $po->supplier_id = $request->supplier_id;
            $po->posting_date = Carbon::createFromFormat('d/m/Y', $request->posting_date)->format('Y-m-d');
            $po->status = 'Dipesan';
            $po->grand_total = $request->grand_total;
            $po->save();

            $this->store_items($request, $po);

            DB::commit();
            return response()->json("success add new data item", 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json($ex->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $id = str_replace("-", "/", $id);
        $result['po'] = PurchaseOrder::selectRaw('purchase_orders.id, DATE_FORMAT(purchase_orders.posting_date, "%d/%m/%Y") AS posting_date, purchase_orders.status, suppliers.name AS supplier_name, suppliers.id AS suuplier_id')
            ->join('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')->where('purchase_orders.id', $id)->first();
        $result["action"] = "update";
        $result['po_items'] = PurchaseOrderItem::join('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_items.purchase_order_id')
            ->join('items', 'items.id', '=', 'purchase_order_items.item_id')
            ->selectRaw("purchase_order_items.item_id, items.name AS item_name, purchase_order_items.qty, purchase_order_items.price, purchase_order_items.amount")
            ->where('purchase_orders.id', $id)->get();

        return view('admin.pages.purchase-order.form', $result);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function get_data(Request $request)
    {
        $query = PurchaseOrder::query()
            ->selectRaw('purchase_orders.id, DATE_FORMAT(purchase_orders.posting_date, "%d-%m-%Y") AS posting_date, purchase_orders.status, suppliers.name AS supplier_name, suppliers.id AS suuplier_id')
            ->join('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id');

        if ($request->filled('id')) {
            $query->where('purchase_orders.id', 'like', "%{$request->id}%");
        }
        if ($request->filled('supplier')) {
            $query->where('suppliers.id', 'like', "%{$request->supplier}%");
        }

        return DataTables::of($query)->toJson();
    }

    function store_items($request, $po)
    {
        foreach (json_decode($request->po_items) as $key => $value) {
            $po_item = new PurchaseOrderItem();
            $po_item->purchase_order_id = $po->id;
            $po_item->item_id = $value->material;
            $po_item->qty = $value->qty;
            $po_item->price = $value->price;
            $po_item->amount = $value->amount;
            $po_item->save();

            $item_stock = ItemStock::where('item_id', $value->material)->first();
            ItemStock::where('item_id', $value->material)->update(['purchase_qty' => $item_stock->purchase_qty + $value->qty]);
        }
    }

    public function calculate_method(Request $request){
        
    }
}
