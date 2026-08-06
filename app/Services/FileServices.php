<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseOrder;
use App\Models\Quote;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf as DomPdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
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
        Log::info('GENERANDO PDF EN SERVICIO DE ARCHIVOS PARA: ', [self::descripcion_general($items)]);
        // Guardar el PDF
        Storage::disk('public')->put($path, $pdf->output());
        // Retornar la URL pública
        return $path;
    }

    public static function url(string $path): string
    {
        return Storage::url($path);
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

        return $items . '/Reporte_'
            . $items . '_'
            . self::replacename($client) . '_'
            . Carbon::parse($model->date)->format('Y-m-d')
            . '.pdf';
    }

    protected static function descripcion_general($items): array
    {
        return ['voucher_type' => $items->voucher_type == 1 ? 'FACTURA ELECTRÓNICA' : 'BOLETA DE VENTA ELECTRÓNICA', 'observacion' => $items->observation ? $items->observation : 'SIN OBSERVACIÓN', 'serie' => $items->serie ? $items->serie : '000', 'correlativo' => $items->correlativo ? $items->correlativo : '00000000', 'fecha' => $items->date ? Carbon::parse($items->date)->format('d/m/Y') : date('d/m/Y'), 'currency' => $items->currency ? $items->currency : 'SOLES', 'total_string' => $items->total_string ? $items->total_string : UtilitisServices::totalEnLetras($items->total), 'subtotal' => $items->subtotal ? $items->subtotal : 0, 'igv' => $items->igv ? $items->igv : 0, 'total' => $items->total ? $items->total : 0, 'forma_pago' => $items->payment_method ? $items->payment_method : 'ESPECIE'];
    }

    /**
     * @return mixed[]
     */
    protected static function dataClientCustomer($modal): array
    {
        $data = [];
        if ($modal instanceof Purchase || $modal instanceof PurchaseOrder) {
            $data['type'] = 'PROVEEDOR';
            $data['identity'] = $modal->supplier->identity ? $modal->supplier->identity : 'DNI';
            $data['document_number'] = $modal->supplier ? $modal->supplier->document_number : '00000000';
            $data['name'] = $modal->supplier ? $modal->supplier->name : '-------';
            $data['address']  = $modal->supplier ? $modal->supplier->address : '------';
            return $data;
        }

        if ($modal instanceof Quote || $modal instanceof Sale) {
            $data['type'] = 'CLIENTE';
            $data['identity'] = $modal->customer->identity ? $modal->customer->identity : 'DNI';
            $data['document_number'] = $modal->customer ? $modal->customer->document_number : '00000000';
            $data['name'] = $modal->customer ? $modal->customer->name : '-------';
            $data['address']  = $modal->customer ? $modal->customer->address : '------';
            return $data;
        }

        throw new \InvalidArgumentException('Modelo no soportado');
    }

    protected static function userInfo(): array
    {
        return [
            'name' => 'Inversiones Isabel <br>',
            'duenio' => 'Malca Goicochea Segundo Manuel',
            'document_number' => '10192555685',
            'address' => 'ASC POPULAR LAS LOMAS DE ANCO MZA 78 LOTE 18 <br>COLEGIO  NACIONAL VILLAS DE ANCON',
            'address_specific' => 'ANCÓN - LIMA - LIMA',
        ];
    }
}
