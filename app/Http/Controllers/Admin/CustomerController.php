<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Str;

class CustomerController extends Controller
{

    public function index()
    {
        $result['title'] = 'Customer';
        return view('admin.pages.customer.index', $result);
    }

    public function create()
    {
        $result['title'] = 'Customer';
        return view('admin.pages.customer.form-customize', $result);
    }

    public function getLinkOptions(Request $request)
    {
        $results = Customer::selectRaw('id, CONCAT(firstname, lastname) AS name')
            ->where('firstname', 'LIKE', '%' . $request->q . '%')
            ->orWhere('lastname', 'LIKE', '%' . $request->q . '%')
            ->limit(50)
            ->get();

        return response()->json($results);
    }

    public function store(Request $request)
    {
        $customer = new Customer();
        $customer->id = Str::random(10);
        $customer->firstname = $request->firstname;
        $customer->lastname = $request->lastname;
        $customer->gender = $request->gender;
        $customer->phone = $request->phone;
        $customer->save();

        return response()->json($customer, 200);
    }

    public function getData(Request $request)
    {
        $search = $request->input('search.value'); // Pencarian global
        $start = $request->input('start');
        $length = $request->input('length');

        $query = Customer::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        }

        $total = $query->count();

        $data = $query->offset($start)
            ->limit($length)
            ->get();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $total,
            'recordsFiltered' => $total, // jika kamu tidak filter, samakan saja
            'data' => $data
        ]);
    }
}
