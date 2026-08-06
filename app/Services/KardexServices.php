<?php

namespace App\Services;

use App\Enum\KardexTypeEnum;
use App\Models\Inventorie;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class KardexServices
{
    public static function getLastRecord(int $product_id, int $warehouse_id): array
    {
        $lastRecord = Inventorie::where('product_id', $product_id)
            ->where('warehouse_id', $warehouse_id)
            ->latest()
            ->first();

        return [
            'quantity_total' => $lastRecord?->quantity_total ?? 0,
            'product_name' => $lastRecord?->product_name ?? Product::find($product_id)?->name ?? '',
            'date' => $lastRecord?->created_at ?? null,
        ];
    }

    public static function registerEntry(
        $model,
        array $product,
        int $warehouse_id,
        string $detail,
        KardexTypeEnum $kardexTypeEnum = KardexTypeEnum::ENTRADA
    ): void {
        $lastRecord = self::getLastRecord($product['id'], $warehouse_id);
        $newQuantity = $lastRecord['quantity_total'] + $product['quantity'];
        self::registerData($model, [
            'detail' => $detail,
            'quantity_in' => $product['quantity'],
            'quantity_total' => $newQuantity,
            'product_name' => $product['name'] ?? $lastRecord['product_name'],
            'type' => $kardexTypeEnum->value,
            'product_id' => $product['id'],
            'warehouse_id' => $warehouse_id,
        ]);
    }

    public static function registerExit(
        $model,
        array $product,
        int $warehouse_id,
        string $detail,
        KardexTypeEnum $kardexTypeEnum = KardexTypeEnum::SALIDA
    ): void {
        $lastRecord = self::getLastRecord($product['id'], $warehouse_id);
        $newQuantity = $lastRecord['quantity_total'] - $product['quantity'];
        self::registerData($model, [
            'detail' => $detail,
            'quantity_out' => $product['quantity'],
            'quantity_total' => $newQuantity,
            'product_name' => $product['name'],
            'type' => $kardexTypeEnum->value,
            'product_id' => $product['id'],
            'warehouse_id' => $warehouse_id,
        ]);
    }

    protected static function registerData($model, array $data): void
    {
        Log::info('Registering inventory movement', $data);
        $model->inventories()->create([
            'detail' => $data['detail'] ?? '',
            'quantity_in' => $data['quantity_in'] ?? 0,
            'quantity_out' => $data['quantity_out'] ?? 0,
            'quantity_balance' => $data['quantity_balance'] ?? 0,
            'product_name' => $data['product_name'] ?? 0,
            'type' => $data['type'] ?? KardexTypeEnum::OTROS->value,
            'quantity_total' => $data['quantity_total'] ?? 0,
            'product_id' => $data['product_id'] ?? 0,
            'warehouse_id' => $data['warehouse_id'] ?? 0,
        ]);
    }
}
