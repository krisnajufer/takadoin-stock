<?php

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\ManufactureController;
use App\Http\Controllers\Admin\MaterialIssueController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\PurchaseReceiptController;
use App\Http\Controllers\Admin\SupplierController;
use App\Models\MaterialIssue;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::controller(ItemController::class)->prefix('/item')->name('item.')->group(function () {
    Route::prefix('/material')->name('material.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/get_data', 'get_data')->name('get_data');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update', 'update')->name('update');
        Route::post('/destroy', 'destroy')->name('destroy');
    });
    Route::prefix('/bouquet')->name('bouquet.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/get_data', 'get_data')->name('get_data');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update', 'update')->name('update');
        Route::post('/destroy', 'destroy')->name('destroy');
        Route::get('/get_data_select', 'get_data_select')->name('get_data_select');
    });
});


Route::controller(SupplierController::class)->prefix('/supplier')->name('supplier.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/get_data', 'get_data')->name('get_data');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update', 'update')->name('update');
    Route::post('/destroy', 'destroy')->name('destroy');
    Route::get('/get_data_select', 'get_data_select')->name('get_data_select');
});

Route::controller(PurchaseOrderController::class)->prefix('/purchase-order')->name('purchase-order.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/get_data', 'get_data')->name('get_data');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update', 'update')->name('update');
    Route::post('/destroy', 'destroy')->name('destroy');
    Route::get('/calculate_method', 'calculate_method')->name('calculate_method');
});

Route::controller(PurchaseReceiptController::class)->prefix('/purchase-receipt')->name('purchase-receipt.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/get_data', 'get_data')->name('get_data');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update', 'update')->name('update');
    Route::post('/destroy', 'destroy')->name('destroy');
    Route::post('/received', 'received')->name('received');
});

Route::controller(ManufactureController::class)->prefix('/manufacture')->name('manufacture.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/get_data', 'get_data')->name('get_data');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update', 'update')->name('update');
    Route::post('/destroy', 'destroy')->name('destroy');
    Route::get('/get_data_bom', 'get_data_bom')->name('get_data_bom');
});

Route::controller(MaterialIssueController::class)->prefix('/material-issue')->name('material-issue.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/get_data', 'get_data')->name('get_data');
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
    Route::get('/edit/{id}', 'edit')->name('edit');
    Route::post('/update', 'update')->name('update');
    Route::post('/destroy', 'destroy')->name('destroy');
});

Route::controller(DashboardController::class)->prefix('/')->name('dashboard.')->group(function () {
    Route::get('/', 'index')->name('index');
});
