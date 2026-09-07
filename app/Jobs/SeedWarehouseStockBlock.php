<?php

namespace App\Jobs;

use App\Enum\KardexTypeEnum;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class SeedWarehouseStockBlock implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 10;

    public int $timeout = 120;

    /**
     * @param array<int> $productIds
     */
    public function __construct(
        public readonly int $warehouseId,
        public readonly string $warehouseName,
        public readonly int $purchaseId,
        public readonly array $productIds,
        public readonly int $stok,
    ) {
    }

    /**
     * Espera creciente entre reintentos automáticos del job completo
     * (10 intentos en total, contando el primero).
     */
    public function backoff(): array
    {
        return [5, 10, 15, 20, 30, 30, 30, 30, 30];
    }

    public function handle(): void
    {
        try {
            // "Ping" para forzar que Azure SQL Serverless despierte de su
            // auto-pause antes de intentar el trabajo real de este bloque.
            DB::select('select 1 as ping');
        } catch (Throwable $exception) {
            // El worker es un proceso de larga duración: si la conexión se
            // cortó, hay que forzar una reconexión antes del próximo intento,
            // o el siguiente ping fallaría contra el mismo handle roto.
            DB::disconnect();
            throw $exception;
        }

        $products = Product::whereIn('id', $this->productIds)
            ->select(['id', 'name', 'code', 'price_purchase'])
            ->get()
            ->keyBy('id');

        $observation = 'Initial stock seeder almacen ID: ' . $this->warehouseId . ' - ' . $this->warehouseName;
        $now = now();

        $existingQuantities = DB::table('records')
            ->where('warehouse_id', $this->warehouseId)
            ->whereIn('product_id', $this->productIds)
            ->pluck('quantity', 'product_id');

        DB::transaction(function () use ($products, $observation, $now, $existingQuantities): void {
            $productableRows = [];
            $inventoryRows = [];

            foreach ($products as $product) {
                $productableRows[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price_type' => 'COMPRA',
                    'price' => $product->price_purchase,
                    'quantity' => $this->stok,
                    'subtotal' => $this->stok * $product->price_purchase,
                    'productable_type' => Purchase::class,
                    'productable_id' => $this->purchaseId,
                    'uuid' => (string) Str::uuid(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $inventoryRows[] = [
                    'detail' => $observation,
                    'quantity_in' => $this->stok,
                    'quantity_out' => 0,
                    'quantity_total' => ($existingQuantities[$product->id] ?? 0) + $this->stok,
                    'product_name' => $product->name,
                    'type' => KardexTypeEnum::ENTRADA->value,
                    'product_id' => $product->id,
                    'warehouse_id' => $this->warehouseId,
                    'inventoryable_type' => Purchase::class,
                    'inventoryable_id' => $this->purchaseId,
                    'uuid' => (string) Str::uuid(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('productables')->insert($productableRows);
            DB::table('inventories')->insert($inventoryRows);

            // El insert masivo no dispara el modelo Eloquent (ni su Observer),
            // así que recuperamos los IDs recién generados por uuid para poder
            // enlazar records.inventory_id, y replicamos manualmente lo que
            // InventorieObserver::created() hace en el flujo normal.
            $insertedInventories = DB::table('inventories')
                ->whereIn('uuid', array_column($inventoryRows, 'uuid'))
                ->get(['id', 'product_id', 'quantity_total']);

            $recordRows = $insertedInventories->map(function ($inventory) use ($products, $observation, $now): array {
                $product = $products->get($inventory->product_id);

                return [
                    'warehouse_id' => $this->warehouseId,
                    'warehouse_name' => $this->warehouseName,
                    'quantity' => $inventory->quantity_total,
                    'product_id' => $inventory->product_id,
                    'product_name' => $product->name,
                    'product_code' => $product->code ?? '',
                    'observation' => $observation,
                    'uuid' => (string) Str::uuid(),
                    'inventory_id' => $inventory->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })->all();

            DB::table('records')->upsert(
                $recordRows,
                ['product_id', 'warehouse_id'],
                ['warehouse_name', 'quantity', 'product_name', 'product_code', 'observation', 'inventory_id', 'updated_at']
            );

            Product::whereIn('id', $this->productIds)->increment('stock', $this->stok);
        });
    }

    public function failed(?Throwable $exception): void
    {
        Log::error(sprintf(
            'SeedWarehouseStockBlock agotó los %d intentos. Almacén: %d (%s), productos: %s. Error: %s',
            $this->tries,
            $this->warehouseId,
            $this->warehouseName,
            implode(',', $this->productIds),
            $exception?->getMessage() ?? 'desconocido'
        ));
    }
}
