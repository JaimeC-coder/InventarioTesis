<?php

namespace App\Http\Controllers\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Response\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validar datos de entrada
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return JsonResponse::error('Datos inválidos', 422, $validator->errors());
        }

        // Log del User-Agent
        Log::info('User-Agent: ' . $request->header('User-Agent'));
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];
        // Verificar si el usuario existe
        $user = User::where('email', $credentials['email'])->first();
        if (!$user) {
            return JsonResponse::error('Usuario no encontrado', 404);
        }

        // Intentar autenticación con JWT
        if (!$token = auth('jwt')->attempt($credentials)) {
            return JsonResponse::error('Credenciales incorrectas', 401);
        }

        return $this->respondWithToken($token);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed', // requiere password_confirmation
        ]);
        if ($validator->fails()) {
            return JsonResponse::error('Datos inválidos', 422, $validator->errors());
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            $token = auth('jwt')->login($user);

            return $this->respondWithToken($token);
        } catch (\Exception $exception) {
            Log::error('Error al registrar usuario: ' . $exception->getMessage());
            return JsonResponse::error('Error al crear usuario', 500);
        }
    }

    public function logout()
    {
        auth('jwt')->logout();

        return JsonResponse::success(null, 'Sesión cerrada exitosamente');
    }

    public function user()
    {
        try {
            $user = auth('jwt')->user();
            if (!$user) {
                return JsonResponse::error('Usuario no encontrado', 404);
            }

            return JsonResponse::success($user, 'Usuario obtenido exitosamente');
        } catch (\Exception $exception) {
            Log::error('Error al obtener usuario: ' . $exception->getMessage());
            return JsonResponse::error('Error al obtener usuario', 500);
        }
    }

    public function refresh()
    {
        try {
            $token = auth('jwt')->refresh();
            return $this->respondWithToken($token);
        } catch (\Exception $exception) {
            Log::error('Error al refrescar token: ' . $exception->getMessage());
            return JsonResponse::error('No se pudo refrescar el token', 401);
        }
    }

    protected function respondWithToken($token)
    {
        return JsonResponse::success([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('jwt')->factory()->getTTL() * 60, // en segundos
            'user' => auth('jwt')->user(),
        ], 'Autenticación exitosa');
    }
}
