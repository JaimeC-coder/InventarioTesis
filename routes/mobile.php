<?php

use App\Http\Controllers\Mobile\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// // Rutas públicas
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);

// Rutas protegidas con JWT
Route::group(['prefix' => 'auth', 'middleware' => 'jwt'], function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/status', function () {
        return response()->json(['mobile' => 'ok']);
    });
});

// // Rutas con JWT
// Route::middleware(['jwt.verify'])->group(function () {
//     Route::get('/profile', [UserController::class, 'profile']);
// });
