<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseOrder;
use App\Models\Quote;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf as DomPdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class FileServices
{
    public static function generatePdfNow(array $payload): string
    {
        $model = $payload['model'];
        $uuid = $payload['uuids'];
        $items = $model::with([
            'products' => function ($q): void {
                $q->orderBy('productables.id')->limit(100);
            },
        ])
            ->where('uuid', $uuid)
            ->firstOrFail();
        $name = self::namefile($items);
        // Carpeta destino dentro de storage/app/public
        $path = 'pdfs/reportes/' . $name;
        $pdf = DomPdf::loadView('export.specific-pdf', [
            'propietario' => self::userInfo(),
            'client'   => self::dataClientCustomer($items),
            'description_general'    => self::descripcion_general($items),
            'products' => $items->products,
        ])->setPaper('a4');
        // Guardar el PDF
        Storage::disk('public')->put($path, $pdf->output());
        // Retornar la URL pública
        return $path;
    }


    public static function url(string $path): string
    {
        return Storage::disk(self::disk())->url($path);
    }

    protected static function disk(): string
    {
        return config('filesystems.default'); // o 'public'
    }


    protected static function replacename($name): string|array
    {
        // quitar a name App\Models\
        $name = str_replace('App\Models\\', '', $name);
        // remplazar espacios por guion bajo
        $name = str_replace(' ', '_', $name);
        return $name;
    }

    protected static function namefile($model): string
    {
        if ($model instanceof Purchase) {
            $items = 'Compra';
            $client = $model->supplier->document_number;
        } elseif ($model instanceof PurchaseOrder) {
            $items = 'Orden_de_Compra';
            $client = $model->supplier->document_number;
        } elseif ($model instanceof Quote) {
            $items = 'Cotizacion';
            $client = $model->customer->document_number;
        } elseif ($model instanceof Sale) {
            $items = 'Venta';
            $client = $model->customer->document_number;
        } else {
            throw new \InvalidArgumentException('Modelo no soportado para generar nombre de archivo');
        }

        return $items.'/Reporte_'
            . $items . '_'
            . self::replacename($client) . '_'
            . Carbon::parse($model->date)->format('Y-m-d')
            . '.pdf';
    }

    protected static function totalEnLetras($monto, $moneda = 'SOLES'): string
    {
        $numberFormatter = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
        $entero = floor($monto);
        $decimales = str_pad(round(($monto - $entero) * 100), 2, '0', STR_PAD_LEFT);

        return mb_strtoupper(
            $numberFormatter->format($entero) . sprintf(' %s CON %s/100', $moneda, $decimales)
        );
    }

    protected static function descripcion_general($items): array
    {
        $data = [];
        if (isset($items->observation) && !empty($items->observation)) {
            $data['observacion'] = $items->observation;
        }

        if (isset($items->serie) && !empty($items->serie)) {
            $data['serie'] = $items->serie;
        }

        if (isset($items->correlativo) && !empty($items->correlativo)) {
            $data['correlativo'] = $items->correlativo;
        }

        if (isset($items->date) && !empty($items->date)) {
            $data['fecha'] = Carbon::parse($items->date)->format('d/m/Y');
        }

        if (isset($items->currency) && !empty($items->currency)) {
            $data['currency'] = $items->currency ?? 'SOLES';
        }

        if (isset($items->total_string) && !empty($items->total_string)) {
            $data['total_string'] = $items->total_string ?? self::totalEnLetras($items->total);
        }

        if (isset($items->subtotal) && !empty($items->subtotal)) {
            $data['subtotal'] = $items->subtotal ?? 0;
        }

        if (isset($items->igv) && !empty($items->igv)) {
            $data['igv'] = $items->igv ?? 0;
        }

        if (isset($items->total) && !empty($items->total)) {
            $data['total'] = $items->total ?? 0;
        }

        return $data;
    }

    /**
     * @return mixed[]
     */
    protected static function dataClientCustomer($modal): array
    {
        $data = [];
        if ($modal instanceof Purchase || $modal instanceof PurchaseOrder) {
            $data['type'] = 'PROVEEDOR';
            $data['identity'] = $modal->supplier->identity ? $modal->supplier->identity->name : 'DNI';
            $data['document_number'] = $modal->supplier ? $modal->supplier->document_number : '00000000';
            $data['name'] = $modal->supplier ? $modal->supplier->name : '-------';
            $data['address']  = $modal->supplier ? $modal->supplier->document_number : '------';
            return $data;
        }

        if ($modal instanceof Quote || $modal instanceof Sale) {
            $data['type'] = 'PROVEEDOR';
            $data['identity'] = $modal->customer->identity ? $modal->customer->identity->name : 'DNI';
            $data['document_number'] = $modal->customer ? $modal->customer->document_number : '00000000';
            $data['name'] = $modal->customer ? $modal->customer->name : '-------';
            $data['address']  = $modal->customer ? $modal->customer->document_number : '------';
            return $data;
        }

        throw new \InvalidArgumentException('Modelo no soportado');
    }

    protected static function userInfo(): array
    {
        return [
            'name' => 'Inversiones Isabel',
            'duenio' => 'Malca Goicochea Segundo Manuel',
            'document_number' => '10192555685',
            'address' => 'Av. Principal 123, Ciudad',
            'address_specific' => 'ANCÓN - LIMA - LIMA',
        ];
    }
}
