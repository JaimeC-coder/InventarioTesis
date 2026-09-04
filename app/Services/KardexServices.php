<?php

namespace App\Services;

use App\Enum\KardexTypeEnum;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KardexServices
{
    public static function getLastRecord(int $product_id): string
    {
        return Product::find($product_id)?->name ?? '';
    }

    public static function registerEntry(
        $model,
        array $product,
        int $warehouse_id,
        string $detail,
        KardexTypeEnum $kardexTypeEnum = KardexTypeEnum::ENTRADA
    ): void {
        $lastRecord = self::getLastRecord($product['id']);
        self::registerData($model, [
            'detail' => $detail,
            'quantity_in' => $product['quantity'],
            'product_name' => $product['name'] ?? $lastRecord,
            'type' => $kardexTypeEnum->value,
            'product_id' => $product['id'],
            'warehouse_id' => $warehouse_id,
        ], isEntry: true);
    }

    public static function registerExit(
        $model,
        array $product,
        int $warehouse_id,
        string $detail,
        KardexTypeEnum $kardexTypeEnum = KardexTypeEnum::SALIDA
    ): void {
        $lastRecord = self::getLastRecord($product['id']);
        self::registerData($model, [
            'detail' => $detail,
            'quantity_out' => $product['quantity'],
            'product_name' => $product['name'] ?? $lastRecord,
            'type' => $kardexTypeEnum->value,
            'product_id' => $product['id'],
            'warehouse_id' => $warehouse_id,
        ], isEntry: false);
    }

    protected static function registerData($model, array $data, bool $isEntry): void
    {
        Log::info('Registering inventory movement', $data);
        DB::transaction(function () use ($model, $data, $isEntry): void {
            // 1. Bloquea (o crea) la fila resumen de este producto+almacén — este es el "mutex" real
            $summary = DB::table('records')
                ->where('product_id', $data['product_id'])
                ->where('warehouse_id', $data['warehouse_id'])
                ->lockForUpdate()
                ->first();
            $previousTotal = $summary->quantity ?? 0;
            $newQuantity = $isEntry
                ? $previousTotal + $data['quantity_in']
                : $previousTotal - $data['quantity_out'];
            // 2. Inserta el movimiento histórico (esto dispara el Observer -> created)
            $model->inventories()->create([
                'detail' => $data['detail'],
                'quantity_in' => $isEntry ? $data['quantity_in'] : 0,
                'quantity_out' => $isEntry ? 0 : $data['quantity_out'],
                'quantity_total' => $newQuantity,
                'product_name' => $data['product_name'] ?? $summary->product_name ?? '',
                'type' => $data['type'] ?? KardexTypeEnum::OTROS->value,
                'product_id' => $data['product_id'],
                'warehouse_id' => $data['warehouse_id'],
            ]);
            // el Observer se encarga de sincronizar stock_summaries y products.stock
            // usando exactamente este mismo quantity_total, así que no hay doble cálculo
        });
    }
}
