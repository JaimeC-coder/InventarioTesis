<?php

namespace Database\Seeders;

use App\Enum\KardexTypeEnum;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\UtilitisServices;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InventorySeeder extends Seeder
{
    /**
     * SQL Server admite un máximo de 2100 parámetros por consulta. La tabla
     * con más columnas por fila (inventories, 13) a este tamaño de lote usa
     * ~1560 parámetros, dejando margen frente a MySQL, que no tiene ese límite.
     */
    private const CHUNK_SIZE = 120;

    /**
     * Permite reanudar el seeder si se interrumpe a mitad de la corrida,
     * en vez de repetir almacenes ya completados desde cero.
     */
    private const CHECKPOINT_FILE = 'seeders/InventorySeeder_checkpoint.json';

    private array $completedWarehouseIds = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::where('is_active_product', 1)
            ->orderBy('id') // orden estable: necesario para que los índices de lote sean reproducibles al reanudar
            ->select(['id', 'name', 'code', 'price_purchase'])
            ->get();
        $warehouses = Warehouse::all();
        $stok = 100;
        $supplierId = Supplier::first()->id; // fratello

        $checkpoint = $this->loadCheckpoint();
        $this->completedWarehouseIds = $checkpoint['completed_warehouse_ids'] ?? [];

        foreach ($warehouses as $warehouse) {
            if (in_array($warehouse->id, $this->completedWarehouseIds, true)) {
                continue;
            }

            $resume = ($checkpoint['in_progress']['warehouse_id'] ?? null) === $warehouse->id
                ? $checkpoint['in_progress']
                : null;

            $this->seedWarehouseStock($warehouse, $products, $stok, $supplierId, $resume);

            $this->completedWarehouseIds[] = $warehouse->id;
            $this->saveCheckpoint(['completed_warehouse_ids' => $this->completedWarehouseIds, 'in_progress' => null]);
        }

        $this->clearCheckpoint();
        $this->command->info('Seeder completado exitosamente.');
    }

    private function seedWarehouseStock(Warehouse $warehouse, Collection $products, int $stok, int $supplierId, ?array $resume): void
    {
        $observation = 'Initial stock seeder almacen ID: ' . $warehouse->id . ' - ' . $warehouse->name;

        if ($resume !== null) {
            $purchase = Purchase::findOrFail($resume['purchase_id']);
            $startChunkIndex = $resume['next_chunk_index'];
            Log::info(sprintf('Reanudando almacén ID: %d - %s desde el lote %d', $warehouse->id, $warehouse->name, $startChunkIndex));
        } else {
            Log::info('Seeding initial stock for warehouse ID: ' . $warehouse->id . ' - ' . $warehouse->name);
            $subtotal = $products->sum(fn(Product $product): float => $stok * $product->price_purchase);
            $igv = $subtotal * 0.18;
            $total = $subtotal + $igv;

            $purchase = Purchase::create([
                'voucher_type' => 1,
                'serie' => 'CM01',
                'correlativo' => (Purchase::max('correlativo') ?? 0) + 1,
                'date' => Carbon::parse('2025-12-15')->format('Y-m-d'),
                'supplier_id' => $supplierId,
                'warehouse_id' => $warehouse->id,
                'status' => 'RECIBIDO',
                'subtotal' => $subtotal,
                'igv' => $igv,
                'total' => $total,
                'total_string' => UtilitisServices::TotalEnLetras($total, 'SOLES'),
                'user_id' => 11,
                'observation' => $observation,
            ]);
            $startChunkIndex = 0;

            // Se guarda ya mismo: si el proceso muere antes de terminar el
            // primer lote, la próxima corrida debe reutilizar esta compra en
            // vez de crear una segunda cabecera duplicada para el almacén.
            $this->saveCheckpoint([
                'completed_warehouse_ids' => $this->completedWarehouseIds,
                'in_progress' => ['warehouse_id' => $warehouse->id, 'purchase_id' => $purchase->id, 'next_chunk_index' => 0],
            ]);
        }

        // Stock ya existente por producto en este almacén, para que el seeder
        // se pueda re-ejecutar sin perder el conteo acumulado (mismo criterio
        // que usaba KardexServices al leer "previousTotal" antes de escribir).
        $existingQuantities = DB::table('records')
            ->where('warehouse_id', $warehouse->id)
            ->pluck('quantity', 'product_id');

        $now = now();
        $chunks = $products->chunk(self::CHUNK_SIZE)->values();

        foreach ($chunks as $chunkIndex => $chunk) {
            if ($chunkIndex < $startChunkIndex) {
                continue; // ya confirmado en una corrida anterior
            }

            DB::transaction(function () use ($chunk, $warehouse, $purchase, $stok, $observation, $existingQuantities, $now): void {
                $productsById = $chunk->keyBy('id');

                $productableRows = [];
                $inventoryRows = [];

                foreach ($chunk as $product) {
                    $productableRows[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'price_type' => 'COMPRA',
                        'price' => $product->price_purchase,
                        'quantity' => $stok,
                        'subtotal' => $stok * $product->price_purchase,
                        'productable_type' => Purchase::class,
                        'productable_id' => $purchase->id,
                        'uuid' => (string) Str::uuid(),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $inventoryRows[] = [
                        'detail' => $observation,
                        'quantity_in' => $stok,
                        'quantity_out' => 0,
                        'quantity_total' => ($existingQuantities[$product->id] ?? 0) + $stok,
                        'product_name' => $product->name,
                        'type' => KardexTypeEnum::ENTRADA->value,
                        'product_id' => $product->id,
                        'warehouse_id' => $warehouse->id,
                        'inventoryable_type' => Purchase::class,
                        'inventoryable_id' => $purchase->id,
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

                $recordRows = $insertedInventories->map(function ($inventory) use ($warehouse, $productsById, $observation, $now): array {
                    $product = $productsById->get($inventory->product_id);

                    return [
                        'warehouse_id' => $warehouse->id,
                        'warehouse_name' => $warehouse->name,
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

                Product::whereIn('id', $chunk->pluck('id'))->increment('stock', $stok);
            });

            $this->saveCheckpoint([
                'completed_warehouse_ids' => $this->completedWarehouseIds,
                'in_progress' => ['warehouse_id' => $warehouse->id, 'purchase_id' => $purchase->id, 'next_chunk_index' => $chunkIndex + 1],
            ]);
        }
    }

    private function loadCheckpoint(): ?array
    {
        if (!Storage::exists(self::CHECKPOINT_FILE)) {
            return null;
        }

        return json_decode(Storage::get(self::CHECKPOINT_FILE), true);
    }

    private function saveCheckpoint(array $data): void
    {
        Storage::put(self::CHECKPOINT_FILE, json_encode($data));
    }

    private function clearCheckpoint(): void
    {
        Storage::delete(self::CHECKPOINT_FILE);
    }
}
