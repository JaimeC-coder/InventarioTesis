<?php

use App\Http\Controllers\GetApiController;
use App\Http\Controllers\ProductController;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Quote;
use App\Models\Reason;
use App\Models\Record;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::post('suppliers', [GetApiController::class, 'suppliers'])->name('admin.suppliers');
Route::post('products_suppliers', [GetApiController::class, 'productsSuppliers'])->name('admin.products_suppliers');
Route::post('products_warehouses', [GetApiController::class, 'productsWarehouses'])->name('admin.products_warehouses');
Route::post('warehouses', [GetApiController::class, 'warehouses'])->name('admin.warehouses');
Route::post('purchases-orders', [GetApiController::class, 'purchasesOrders'])->name('admin.purchases-orders');
Route::post('quotes', [GetApiController::class, 'quotes'])->name('admin.quotes');
Route::post('customers', [GetApiController::class, 'customers'])->name('admin.customers');
Route::post('reasons', [GetApiController::class, 'reasons'])->name('admin.reasons');
Route::post('categories', [GetApiController::class, 'categories'])->name('admin.categories');
Route::post('units', [GetApiController::class, 'units'])->name('admin.units');
Route::post('measures', [GetApiController::class, 'measures'])->name('admin.measures');
Route::post('baseProducts', [GetApiController::class, 'baseProducts'])->name('admin.baseProducts');
Route::post('massive-products', [ProductController::class, 'massiveProducts'])->name('admin.massive-products');
