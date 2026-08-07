<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    public static function NextCorrelative(string $model): int
    {
        return (int) DB::table((new $model())->getTable())
            ->lockForUpdate()
            ->max('correlativo') + 1;
    }

    public static function generateAndAttachPdf(string $modelClass, $model): void
    {
        $fileDirection = FileServices::generatePdfNow(['model' => $modelClass, 'uuids' => $model->uuid]);
        $model->update(['file_path' => $fileDirection]);
        Log::info('File generated at: ' . $fileDirection);
    }
}
