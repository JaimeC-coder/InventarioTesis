<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ProductController;
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


Route::resource('categories', CategoryController::class)->except(['show']);
Route::resource('products', ProductController::class)->except(['show']);

Route::post('products/{product}/images', [ProductController::class, 'uploadImages'])->name('products.uploadImages');

Route::delete('images/{image}', [ImageController::class, 'destroy'])->name('image.destroy');
