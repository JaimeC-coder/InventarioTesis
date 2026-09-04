<?php

use App\Http\Controllers\Mobile\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// Rutas públicas (sin autenticación)
Route::group(['prefix' => 'auth'], function (): void {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// Rutas protegidas con JWT
Route::middleware('auth:jwt')->group(function (): void {
    // Auth
    Route::group(['prefix' => 'auth'], function (): void {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']); // Nueva: refrescar token
        Route::get('/user', [AuthController::class, 'user']);
        Route::get('/status', function () {
            return response()->json([
                'success' => true,
                'message' => 'API Mobile funcionando correctamente',
                'timestamp' => now()->toIso8601String(),
            ]);
        });
    });
    // Clientes
    Route::group(['prefix' => 'clients'], function (): void {
        Route::get('/', [\App\Http\Controllers\Mobile\Client\ClientController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Mobile\Client\ClientController::class, 'store']);
        Route::get('/{uuid}', [\App\Http\Controllers\Mobile\Client\ClientController::class, 'show']); // ✅ Corregido
        Route::put('/{uuid}', [\App\Http\Controllers\Mobile\Client\ClientController::class, 'update']);
        Route::delete('/{uuid}', [\App\Http\Controllers\Mobile\Client\ClientController::class, 'destroy']);
    });
});
