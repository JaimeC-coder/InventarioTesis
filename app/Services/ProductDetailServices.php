<?php

namespace App\Services;

use App\Enum\KardexTypeEnum;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductDetailServices
{
    /**
     * Crea los detalles de los productos para una compra.
     *
     * @param $modelo
     */
    public static function createDetailproductableRegister($modelo, array $products, int $warehouse_id, string $observation): void
    {
        self::syncProductDetails($modelo, $products, $warehouse_id, 'entry', $observation);
    }

    public static function createDetailproductableExit($modelo, array $products, int $warehouse_id, string $observation): void
    {
        self::syncProductDetails($modelo, $products, $warehouse_id, 'exit', $observation);
    }

    /**
     * Traslado de productos entre dos almacenes.
     * Genera un movimiento de Salida (Traslado-IGS) en el almacén origen
     * y un movimiento de Entrada (Traslado-IGD) en el almacén destino.
     */
    public static function createDetailproductableTransfer(
        $modelo,
        array $products,
        int $fromWarehouseId,
        int $toWarehouseId
    ): void {
        Log::info('productos a trasladar: ', $products);
        self::validateProductsExist($products);
        if ($fromWarehouseId === $toWarehouseId) {
            throw new \InvalidArgumentException('El almacén origen y destino no pueden ser el mismo.');
        }

        DB::transaction(function () use ($modelo, $products, $fromWarehouseId, $toWarehouseId): void {
            $modelo->products()->attach(self::buildPivotData($products));
            $fromWarehouseName = Warehouse::whereKey($fromWarehouseId)->value('name');
            $toWarehouseName = Warehouse::whereKey($toWarehouseId)->value('name');
            foreach ($products as $product) {
                KardexServices::registerExit(
                    $modelo,
                    $product,
                    $fromWarehouseId,
                    'Traslado-IGS ' . $toWarehouseName,
                    KardexTypeEnum::TRASLADO_IGS
                );
                KardexServices::registerEntry(
                    $modelo,
                    $product,
                    $toWarehouseId,
                    'Traslado-IGD ' . $fromWarehouseName,
                    KardexTypeEnum::TRASLADO_IGD
                );
            }
        });
    }

    private static function syncProductDetails($modelo, array $products, int $warehouse_id, string $movementType, string $observation): void
    {
        self::validateProductsExist($products);
        DB::transaction(function () use ($modelo, $products, $warehouse_id, $movementType, $observation): void {
            $modelo->products()->attach(self::buildPivotData($products));
            foreach ($products as $product) {
                $movementType === 'entry'
                    ? KardexServices::registerEntry($modelo, $product, $warehouse_id, $observation, KardexTypeEnum::ENTRADA)
                    : KardexServices::registerExit($modelo, $product, $warehouse_id, $observation, KardexTypeEnum::SALIDA);
            }
        });
    }

    private static function validateProductsExist(array $products): void
    {
        $requestedIds = array_column($products, 'id');
        $existingIds = Product::whereIn('id', $requestedIds)->pluck('id')->all();
        $missingIds = array_diff($requestedIds, $existingIds);
        if ($missingIds !== []) {
            throw new \InvalidArgumentException(
                'Los siguientes productos no existen: ' . implode(', ', $missingIds)
            );
        }
    }

    private static function buildPivotData(array $products): array
    {
        $pivotData = [];
        foreach ($products as $product) {
            $pivotData[$product['id']] = [
                'product_name' => $product['name'],
                'price_type'   => $product['price_type'],
                'quantity'     => $product['quantity'],
                'price'        => $product['price'],
                'subtotal'     => $product['quantity'] * $product['price'],
            ];
        }

        return $pivotData;
    }
}
