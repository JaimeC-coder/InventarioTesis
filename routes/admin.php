<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.dashboard');
})->name('dashboard');
Route::get('/ecommerce', function () {
    return view('admin.ecommerce');
})->name('ecommerce');
Route::get('/users', function () {
    return view('admin.users');
})->name('users');

Route::get('/settings', function () {
    return view('admin.settings');
})->name('settings');
Route::get('/logout', function () {
    return view('admin.logout');
})->name('logout');

//Inventario
Route::resource('categories', CategoryController::class)->except(['show']);
Route::resource('products', ProductController::class)->except(['show']);
Route::resource('warehouses', WarehouseController::class)->except(['show']);

Route::post('products/{product}/images', [ProductController::class, 'uploadImages'])->name('products.uploadImages');
//Ventas
Route::resource('customers', CustomerController::class)->except(['show']);

Route::resource('quotes', QuoteController::class)->only(['index', 'create', 'store']);
//Compras
Route::resource('suppliers', SupplierController::class)->except(['show']);
Route::resource('purchases-orders', PurchaseOrderController::class)->only(['index', 'create', 'store']);
Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store']);

Route::delete('images/{image}', [ImageController::class, 'destroy'])->name('image.destroy');
