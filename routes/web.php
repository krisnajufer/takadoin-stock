<?php

use App\Http\Controllers\Admin\CustomerController;
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

// Route::get('/', function () {
//     return view('admin.pages.customer.form-customize');
// });
// Route::get('/customer', function () {
//     return view('admin.pages.customer.index');
// });
// Route::get('/customer/create', function () {
//     return view('admin.pages.customer.form');
// });

Route::controller(CustomerController::class)->prefix('/customer/')->name('customer.')->group(function () {
    Route::get('getLinkOptions', 'getLinkOptions')->name('getLinkOptions');
    Route::get('', 'index')->name('index');
    Route::get('/getData', 'getData')->name('getData');
    Route::get('/add', 'create')->name('add');
    Route::post('/store', 'store')->name('store');
});
