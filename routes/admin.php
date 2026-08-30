<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

/**
 * route inventario
 */
Route::get('/ecommerce', function (): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View {
    return view('admin.ecommerce');
})->name('ecommerce');

Route::get('/', function (): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View {
    return view('admin.dashboard');
})->name('dashboard');
Route::get('/chatbot', function (): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View {
    return view('admin.chatbot');
})->name('chatbot');



Route::get('/settings', function (): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View {
    return view('admin.settings');
})->name('settings');
Route::get('/logout', function (): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View {
    return view('admin.logout');
})->name('logout');

//Inventario
Route::resource('categories', CategoryController::class)->except(['show']);
Route::resource('products', ProductController::class)->except(['show']);
Route::resource('warehouses', WarehouseController::class)->except(['show']);

Route::get('/units', function (): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View {
    return view('admin.units');
})->name('units.index');

Route::get('/measures', function (): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View {
    return view('admin.measures');
})->name('measures.index');

Route::post('products/{product}/images', [ProductController::class, 'uploadImages'])->name('products.uploadImages');
//Ventas
Route::resource('customers', CustomerController::class)->except(['show']);
Route::resource('quotes', QuoteController::class)->only(['index', 'create', 'store']);
Route::resource('sales', SaleController::class)->only(['index', 'create', 'store']);
//Compras
Route::resource('suppliers', SupplierController::class)->except(['show']);
Route::resource('purchases-orders', PurchaseOrderController::class)->only(['index', 'create', 'store']);
Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store']);
//Movimientos
Route::resource('movements', MovementController::class)->only(['index', 'create', 'store']);
Route::resource('transfers', TransferController::class)->only(['index', 'create', 'store']);

Route::delete('images/{image}', [ImageController::class, 'destroy'])->name('image.destroy');

//Configuraciones
Route::resource('users', \App\Http\Controllers\UserController::class)->except(['show', 'destroy', 'update']);
Route::resource('roles', \App\Http\Controllers\RolController::class)->except(['show', 'destroy', 'update']);
Route::get('permissions', [\App\Http\Controllers\RolController::class, 'permissionsIndex'])->name('permissions.index');
