<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
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
            $item->name = $request->name;
            $item->is_material = isset($request->is_material) ? $request->is_material : 0;
            $item->save();
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
        $result['item'] = Item::find($id);
        $result["action"] = "update";
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

            DB::commit();
            return response()->json("success deleted data " . $url[4], 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json($ex->getMessage(), 500);
        }
    }

    public function get_data(Request $request)
    {
        $query = Item::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', "%{$request->name}%");
        }

        if ($request->filled('is_material') && $request->is_material != "") {
            $query->where('is_material', '=', $request->is_material);
        }

        return DataTables::of($query)->toJson();
    }
}
