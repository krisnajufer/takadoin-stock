<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
}
