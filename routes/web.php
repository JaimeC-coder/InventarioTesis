<?php

use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function (): void {
    Route::get('/dashboard', function (): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('prueba', [SaleController::class, 'showPDF'])->name('prueba');
