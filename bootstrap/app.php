<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web', 'auth')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
            // Mobile routes
            Route::prefix('mobile')
                ->name('mobile.')
                ->group(base_path('routes/mobile.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // 1. Token expirado (captura directa)
        $exceptions->renderable(function (TokenExpiredException $e, $request) {
            return response()->json([
                'success' => false,
                'message' => 'Token expirado',
            ], 401);
        });
        // 2. Token inválido (captura directa)
        $exceptions->renderable(function (TokenInvalidException $e, $request) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido',
            ], 401);
        });
        // 3. UnauthorizedHttpException (cuando JWT envuelve las excepciones)
        $exceptions->renderable(function (UnauthorizedHttpException $e, $request) {
            if ($request->expectsJson()) {
                $previous = $e->getPrevious();
                // Verificar la excepción interna
                if ($previous instanceof TokenExpiredException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Token expirado',
                    ], 401);
                }
                if ($previous instanceof TokenInvalidException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Token inválido',
                    ], 401);
                }
                if ($previous instanceof JWTException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Token no proporcionado',
                    ], 401);
                }
                // Si no hay token
                if (!$request->hasHeader('Authorization') || empty($request->bearerToken())) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Token no proporcionado',
                    ], 401);
                }
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado',
                ], 401);
            }
        });
        // 4. JWTException genérica
        $exceptions->renderable(function (JWTException $e, $request) {
            return response()->json([
                'success' => false,
                'message' => 'Token no proporcionado',
            ], 401);
        });
        // 5. AuthenticationException (fallback general)
        $exceptions->renderable(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                // Si no hay token
                if (!$request->hasHeader('Authorization') || empty($request->bearerToken())) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Token no proporcionado',
                    ], 401);
                }
                return response()->json([
                    'success' => false,
                    'message' => 'No autorizado',
                ], 401);
            }
        });
    })->create();
