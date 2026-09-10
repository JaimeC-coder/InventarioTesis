<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:jwt','role:Administrador,jwt','throttle:10,1'])->group(function (): void {
    Route::post('massive-products', [ProductController::class, 'massiveProducts'])->name('admin.massive-products');
});
