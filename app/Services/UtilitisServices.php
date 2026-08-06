<?php

namespace App\Services;

class UtilitisServices
{
    public static function TotalEnLetras($numero = 0, $moneda = 'SOLES'): String
    {
        $numberFormatter = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
        $entero = floor($numero);
        $decimales = str_pad(round(($numero - $entero) * 100), 2, '0', STR_PAD_LEFT);

        return mb_strtoupper(
            $numberFormatter->format($entero) . sprintf(' %s CON %s/100', $moneda, $decimales)
        );
    }
}
