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
        return view('admin.pages.item.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $result["action"] = "store";
        return view('admin.pages.item.form', $result);
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
        //
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
