<?php

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ItemController;
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
