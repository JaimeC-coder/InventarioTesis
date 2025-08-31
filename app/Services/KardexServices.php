<?php

namespace App\Services;

use App\Models\Inventorie;
use App\Models\Product;

class KardexServices
{
    public static function getLastRecord($product_id, $warehouse_id): array
    {
        $lastRecort = Inventorie::where('product_id', $product_id)
            ->where('warehouse_id', $warehouse_id)
            ->latest()
            ->first();

        return [
            'quantity_balance' => $lastRecort?->quantity_balance ?? 0,
            'total_balance' => $lastRecort?->total_balance ?? 0,
            'cost_balance' => $lastRecort?->cost_balance ?? 0,
            'date' => $lastRecort?->created_at ?? null,
        ];
    }

    public static function registerEntry($model, array $product, $warehouse_id, $detail): void
    {
        $lastRecord = self::getLastRecord($product['id'], $warehouse_id);
        $newQuantity = $lastRecord['quantity_balance'] + $product['quantity'];
        $newTotal = $lastRecord['total_balance'] + ($product['quantity'] * $product['price']);
        $costBalance = $newTotal / $newQuantity;
        $newregister = [
            'detail' => $detail,
            'quantity_in' => $product['quantity'],
            'cost_in' => $product['price'],
            'total_in' => $product['quantity'] * $product['price'],
            'quantity_balance' => $newQuantity,
            'cost_balance' => $costBalance,
            'total_balance' => $newTotal,
            'product_id' => $product['id'],
            'warehouse_id' => $warehouse_id,
        ];
        self::registerData($model, $newregister);
        self::updateProductStock($product['id'], $product['quantity'], 'add');
    }

    public static function registerExit($model, array $product, $warehouse_id, $detail): void
    {
        $lastRecord = self::getLastRecord($product['id'], $warehouse_id);
        $newQuantity = $lastRecord['quantity_balance'] - $product['quantity'];
        $newTotal = $lastRecord['total_balance'] - ($product['quantity'] * $lastRecord['cost_balance']);
        $costBalance = $newTotal / ($newQuantity ?: 1);
        $newregister = [
            'detail' => $detail,
            'cost_out' => $lastRecord['cost_balance'],
            'total_out' => $product['quantity'] * $lastRecord['cost_balance'],
            'quantity_out' => $product['quantity'],
            'quantity_balance' => $newQuantity,
            'cost_balance' => $costBalance,
            'total_balance' => $newTotal,
            'product_id' => $product['id'],
            'warehouse_id' => $warehouse_id,
        ];
        self::registerData($model, $newregister);
        self::updateProductStock($product['id'], $product['quantity'], 'subtract');
    }

    public static function registerMovement($model, array $product, $warehouse_id, $detail, $type): void
    {
        $lastRecord = self::getLastRecord($product['id'], $warehouse_id);
        if ($type == 1) { // Entry
            $newQuantity = $lastRecord['quantity_balance'] + $product['quantity'];
            $newTotal = $lastRecord['total_balance'] + ($product['quantity'] * $product['price']);
            $costBalance = $newTotal / $newQuantity;
        } elseif ($type == 2) { // Exit
            $newQuantity = $lastRecord['quantity_balance'] - $product['quantity'];
            $newTotal = $lastRecord['total_balance'] - ($product['quantity'] * $lastRecord['cost_balance']);
            $costBalance = $newTotal / ($newQuantity ?: 1);
        }

        $newregister = [
            'detail' => $detail,
            'quantity_in' => $product['quantity'],
            'cost_in' => $product['price'],
            'total_in' => $product['quantity'] * $product['price'],
            'quantity_balance' => $newQuantity,
            'cost_balance' => $costBalance,
            'total_balance' => $newTotal,
            'product_id' => $product['id'],
            'warehouse_id' => $warehouse_id,
        ];
        self::registerData($model, $newregister);
    }

    protected static function registerData($model, array $data)
    {
        $model->inventories()->create([
            'detail' => $data['detail'] ?: '',
            'quantity_in' => $data['quantity_in'] ?: 0,
            'cost_in' => $data['cost_in'] ?: 0,
            'total_in' => $data['total_in'] ?: 0,
            'quantity_out' => $data['quantity_out'] ?: 0,
            'cost_out' => $data['cost_out'] ?: 0,
            'total_out' => $data['total_out'] ?: 0,
            'quantity_balance' => $data['quantity_balance'] ?: 0,
            'cost_balance' => $data['cost_balance'] ?: 0,
            'total_balance' => $data['total_balance'] ?: 0,
            'product_id' => $data['product_id'] ?: 0,
            'warehouse_id' => $data['warehouse_id'] ?: 0,
        ]);
    }

    public function updateProductStock($productId, $quantity, $operation): void
    {
        $product = Product::find($productId);
        if ($product) {
            if ($operation === 'add') {
                $product->stock += $quantity;
            } elseif ($operation === 'subtract') {
                $product->stock -= $quantity;
            }

            $product->save();
        }
    }
}
