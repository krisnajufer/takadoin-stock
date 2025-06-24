<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.pages.supplier.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $result["action"] = "store";
        return view('admin.pages.supplier.form', $result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Supplier::rules());

        if ($validator->fails()) {
            return response()->json($validator->getMessageBag(), 400);
        }

        DB::beginTransaction();
        try {
            $supplier = new Supplier();
            $supplier->id = Supplier::get_new_code();
            $supplier->name = $request->name;
            $supplier->address = $request->address;
            $supplier->phone = $request->phone;
            $supplier->save();

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
        $result['supplier'] = Supplier::find($id);
        $result["action"] = "update";
        return view('admin.pages.supplier.form', $result);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), Supplier::rules());

        if ($validator->fails()) {
            return response()->json($validator->getMessageBag(), 400);
        }


        DB::beginTransaction();
        try {
            $supplier = Supplier::find($request->id);
            $supplier->name = $request->name;
            $supplier->address = $request->address;
            $supplier->phone = $request->phone;
            $supplier->save();
            DB::commit();
            return response()->json("success updated data supplier", 200);
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
        DB::beginTransaction();
        try {
            $ids = $request->json()->all();
            Supplier::whereIn('id', $ids['data'])->delete();

            DB::commit();
            return response()->json("success deleted data suppliers", 200);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json($ex->getMessage(), 500);
        }
    }

    public function get_data(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('id')) {
            $query->where('id', 'like', "%{$request->id}%");
        }
        if ($request->filled('name')) {
            $query->where('name', 'like', "%{$request->name}%");
        }

        return DataTables::of($query)->toJson();
    }

    public function get_data_select(Request $request)
    {
        $query = Supplier::query()
            ->selectRaw('id AS id, name AS text');

        if ($request->filled('search')) {
            $query->where('id', 'like', "%{$request->search}%")
                ->orWhere('name', 'like', "%{$request->search}%");
        }

        // Optional: Tambahkan limit untuk performa
        $results = $query->limit(10)->get();
        // dd($results);
        return response()->json([
            'results' => $results
        ]);
    }
}
