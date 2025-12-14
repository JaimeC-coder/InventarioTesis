<?php

namespace App\Http\Controllers\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:api', except: ['login']),
        ];
    }

    public function login(Request $request)
    {
        // quiero ver por consola el valor de X-Client-App
        Log::info('X-Client-App: ' . $request->header('User-Agent'));
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];
        $user  = User::where('email', $credentials['email'])->first();
        if (!$user) {
            return JsonResponse::error('Usuario no encontrado', 404);
        }

        if (! $token = auth('jwt')->attempt($credentials)) {
            return JsonResponse::error('Credenciales incorrectas', 401);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return JsonResponse::success(auth('jwt')->user());
    }

    public function logout()
    {
        auth('jwt')->logout();

        return response()->json(['mensaje' => 'Cierre de sesión exitoso']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('jwt')->refresh());
    }

    protected function respondWithToken($token)
    {
        $data = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('jwt')->factory()->getTTL() * 60,
        ];

        return JsonResponse::success($data, 'Inicio de sesión exitoso', 200);
    }
}
