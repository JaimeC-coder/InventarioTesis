<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GetApiController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\MeasureController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

//Dashboard
Route::group(['prefix' => '/'], function (): void {
    Route::get('ecommerce', [DashboardController::class, 'ecommerce'])->name('ecommerce');
    Route::get('', [DashboardController::class, 'dashboard1'])->name('dashboard');
});

Route::group(['prefix' => 'reportes', 'as' => 'reportes.'], function (): void {
    Route::get('/ventas', [ChatbotController::class, 'ventas'])->name('ventas');
    Route::get('/compras', [ChatbotController::class, 'compras'])->name('compras');
    Route::get('/inventario', [ChatbotController::class, 'inventario'])->name('inventario');
});
//'as' => 'chatbot.',
Route::group(['prefix' => 'chatbot',  'middleware' => ['auth', 'signed']], function (): void {
    Route::get('/', [DashboardController::class, 'chatbot'])->name('chatbot');
    Route::get('/reportes/descargar/{filename}', [ChatbotController::class, 'downloadReport'])
        ->name('chatbot.download');
    Route::post('/message', [ChatbotController::class, 'message'])->name('chatbot.message');
    Route::post('/execute-metric', [ChatbotController::class, 'executeMetric'])->name('chatbot.execute-metric');
});

//Inventario
Route::resource('categories', CategoryController::class)->except(['show']);
Route::resource('products', ProductController::class)->except(['show']);
Route::resource('warehouses', WarehouseController::class)->except(['show']);
//Ventas
Route::resource('customers', CustomerController::class)->except(['show']);
Route::resource('quotes', QuoteController::class)->only(['index', 'create', 'store']);
Route::resource('sales', SaleController::class)->only(['index', 'create', 'store']);
Route::resource('measures', MeasureController::class)->except(['show']);
Route::resource('units', UnitController::class)->except(['show']);
//Compras
Route::resource('suppliers', SupplierController::class)->except(['show']);
Route::resource('purchases-orders', PurchaseOrderController::class)->only(['index', 'create', 'store']);
Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store']);
//Movimientos
Route::resource('movements', MovementController::class)->only(['index', 'create', 'store']);
Route::resource('transfers', TransferController::class)->only(['index', 'create', 'store']);
//Imagenes
Route::delete('images/{image}', [ImageController::class, 'destroy'])->name('image.destroy');
Route::post('products/{product}/images', [ProductController::class, 'uploadImages'])->name('products.uploadImages');
//Configuraciones
Route::resource('users', \App\Http\Controllers\UserController::class)->except(['show', 'destroy', 'update']);
Route::resource('roles', \App\Http\Controllers\RolController::class)->except(['show', 'destroy', 'update']);
Route::get('permissions', [\App\Http\Controllers\RolController::class, 'permissionsIndex'])->name('permissions.index');

//RUtas api consumer web
Route::middleware(['throttle:60,1'])->group(function (): void {
    Route::post('suppliers', [GetApiController::class, 'suppliers'])->name('suppliers');
    Route::post('products_suppliers', [GetApiController::class, 'productsSuppliers'])->name('products_suppliers');
    Route::post('products_warehouses', [GetApiController::class, 'productsWarehouses'])->name('products_warehouses');
    Route::post('warehouses', [GetApiController::class, 'warehouses'])->name('warehouses');
    Route::post('purchases-orders', [GetApiController::class, 'purchasesOrders'])->name('purchases-orders');
    Route::post('quotes', [GetApiController::class, 'quotes'])->name('quotes');
    Route::post('customers', [GetApiController::class, 'customers'])->name('customers');
    Route::post('reasons', [GetApiController::class, 'reasons'])->name('reasons');
    Route::post('categories', [GetApiController::class, 'categories'])->name('categories');
    Route::post('units', [GetApiController::class, 'units'])->name('units');
    Route::post('measures', [GetApiController::class, 'measures'])->name('measures');
    Route::post('roles', [GetApiController::class, 'roles'])->name('list-roles');
    Route::post('baseProducts', [GetApiController::class, 'baseProducts'])->name('baseProducts');
});
