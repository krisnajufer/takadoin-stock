<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CoreController extends Controller
{
    public function getLinkOptions($model, Request $request)
    {
        $modelClass = 'App\\Models\\' . Str::studly($model);

        if (!class_exists($modelClass)) {
            return response()->json(['error' => 'Invalid model'], 404);
        }

        // Tambahkan pencarian jika ada query dari select2
        $query = $request->get('q');

        $results = $modelClass::when($query, function ($q) use ($query) {
            return $q->where('name', 'like', '%' . $query . '%');
        })
            ->select('id', 'name') // pastikan model punya kolom 'id' dan 'name'
            ->limit(50)
            ->get();

        return response()->json($results);
    }
}
