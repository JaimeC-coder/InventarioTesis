<?php

namespace App\Services;

class DocumentServices
{
    public static function getDataFromRUC($ruc): mixed
    {
        $token = config('app.apis_peru');
        $url = sprintf('https://dniruc.apisperu.com/api/v1/ruc/%s?token=%s', $ruc, $token);
        $response = file_get_contents($url);
        return json_decode($response, true);
    }

    public static function getDataFromDNI($dni): mixed
    {
        $token = config('app.apis_peru');
        $url = sprintf('https://dniruc.apisperu.com/api/v1/dni/%s?token=%s', $dni, $token);
        $response = file_get_contents($url);
        return json_decode($response, true);
    }
}
