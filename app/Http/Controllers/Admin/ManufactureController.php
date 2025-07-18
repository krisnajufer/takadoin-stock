<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bom;
use App\Models\Manufacture;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

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
            ->selectRaw('items.name as material, item_stocks.actual_qty as current_qty, bom_materials.qty * ' . $request->qty . ' AS needed_qty')
            ->join('bom_materials', 'boms.id', '=', 'bom_materials.bom_id')
            ->join('items', 'bom_materials.item_id', '=', 'items.id')
            ->join('item_stocks', 'items.id', '=', 'item_stocks.item_id')
            ->where('boms.item_id', $request->bouquet)->get();

        return $query;
    }
}
