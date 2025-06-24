<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bom;
use App\Models\BomMaterial;
use App\Models\Item;
use App\Models\ItemStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $url = explode("/", url()->current());
        return view('admin.pages.' . $url[4] . '.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $result["action"] = "store";
        $url = explode("/", url()->current());
        $result["materials"] = Item::where("is_material", 1)->get();
        return view('admin.pages.' . $url[4] . '.form', $result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Item::rules());

        if ($validator->fails()) {
            return response()->json($validator->getMessageBag(), 400);
        }

        DB::beginTransaction();
        try {
            $item = new Item();
            $item->id = Item::get_new_code($request->is_material);
            $item->name = $request->name;
            $item->is_material = $request->is_material;
            $item->save();

            if ($request->is_material < 1) {
                $this->store_bom($request, $item);
            } else {
                $item_stock = new ItemStock();
                $item_stock->item_id = $item->id;
                $item_stock->save();
            }

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
        $url = explode("/", url()->current());
        $id = str_replace("-", "/", $id);
        $result['item'] = Item::find($id);
        $result["action"] = "update";
        if ($url[4] == "bouquet") {
            $result['boms'] = BomMaterial::join('boms', 'boms.id', '=', 'bom_materials.bom_id')
                ->join('items', 'items.id', '=', 'bom_materials.item_id')
                ->selectRaw("bom_materials.item_id, items.name AS item_name, bom_materials.qty")
                ->where('boms.item_id', $id)->get();
        }
        return view('admin.pages.' . $url[4] . '.form', $result);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $url = explode("/", url()->current());
        $validator = Validator::make($request->all(), Item::rules());

        if ($validator->fails()) {
            return response()->json($validator->getMessageBag(), 400);
        }


        DB::beginTransaction();
        try {
            $item = Item::find($request->id);
            if (!isset($request->is_material)) {
            }
            $item->name = $request->name;
            $item->save();
            DB::commit();
            return response()->json("success updated data " . $url[4], 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json($ex->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $url = explode("/", url()->current());
        DB::beginTransaction();
        try {
            $ids = $request->json()->all();
            Item::whereIn('id', $ids['data'])->delete();
            ItemStock::whereIn('item_id', $ids['data'])->delete();

            DB::commit();
            return response()->json("success deleted data " . $url[4], 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json($ex->getMessage(), 500);
        }
    }

    public function get_data(Request $request)
    {
        $query = Item::query()
            ->leftjoin('item_stocks', 'items.id', '=', 'item_stocks.item_id')
            ->selectRaw('items.id AS id, items.name AS name,  items.is_material AS is_material, item_stocks.actual_qty AS qty');

        if ($request->filled('id')) {
            $query->where('items.id', 'like', "%{$request->id}%");
        }
        if ($request->filled('name')) {
            $query->where('items.name', 'like', "%{$request->name}%");
        }

        if ($request->filled('is_material') && $request->is_material != "") {
            $query->where('items.is_material', '=', $request->is_material);
        }

        return DataTables::of($query)->toJson();
    }

    public function get_data_select(Request $request)
    {
        $query = Item::query()
            ->join('item_stocks', 'items.id', '=', 'item_stocks.item_id')
            ->selectRaw('items.id AS id, items.name AS text')
            ->where([
                ['items.is_material', '=', $request->is_material],
                ['item_stocks.actual_qty', '>=', 0]
            ]);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('items.id', 'like', "%{$request->search}%")
                    ->orWhere('items.name', 'like', "%{$request->search}%");
            });
        }

        // Optional: Tambahkan limit untuk performa
        $results = $query->limit(10)->get();
        // dd($results);
        return response()->json([
            'results' => $results
        ]);
    }

    function store_bom($request, $item)
    {
        $bom = new Bom();
        $bom->id = Bom::get_new_code();
        $bom->item_id = $item->id;
        $bom->save();

        foreach (json_decode($request->bom_items) as $key => $value) {
            $bom_material = new BomMaterial();
            $bom_material->bom_id = $bom->id;
            $bom_material->item_id = $value->material;
            $bom_material->qty = $value->qty;
            $bom_material->save();
        }
    }
}
