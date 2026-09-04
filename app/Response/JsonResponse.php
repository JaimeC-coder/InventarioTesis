<?php

namespace App\Response;

class JsonResponse
{
    public static function success($data, $message = 'Success', $code = 200)
    {
        return response()->json([
            'status' => true,
            'data' => $data,
        ], $code);
    }

    public static function error($message = 'Error', $code = 400, $errors = [])
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }
}
