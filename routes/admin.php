<?php

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
Route::get('/products', function () {
    return view('admin.products');
})->name('products');
Route::get('/settings', function () {
    return view('admin.settings');
})->name('settings');
Route::get('/logout', function () {
    return view('admin.logout');
})->name('logout');
Route::get('/products', function () {
    return view('admin.products');
})->name('products');
