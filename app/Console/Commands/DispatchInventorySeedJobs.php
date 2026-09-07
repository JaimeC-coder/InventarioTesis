<?php

namespace App\Console\Commands;

use App\Jobs\SeedWarehouseStockBlock;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\UtilitisServices;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DispatchInventorySeedJobs extends Command
{
    protected $signature = 'inventory:dispatch-seed-jobs';

    protected $description = 'Despacha a la cola inventory-seed, un batch por almacén, los bloques de stock inicial con reintento automático ante cortes de Azure SQL.';

    private const CHUNK_SIZE = 120;

    private const STOCK_PER_PRODUCT = 100;

    public function handle(): int
    {
        $this->waitForDatabase();
        $productIds = Product::where('is_active_product', 1)->orderBy('id')->pluck('id');
        $warehouses = Warehouse::all();
        $supplierId = Supplier::first()?->id;
        if ($productIds->isEmpty() || $warehouses->isEmpty() || !$supplierId) {
            $this->error('Faltan productos activos, almacenes o proveedor. Se aborta.');
            return self::FAILURE;
        }

        $subtotal = DB::table('products')->where('is_active_product', 1)->sum('price_purchase') * self::STOCK_PER_PRODUCT;
        $igv = $subtotal * 0.18;
        $total = $subtotal + $igv;
        foreach ($warehouses as $warehouse) {
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
                'observation' => 'Initial stock seeder almacen ID: ' . $warehouse->id . ' - ' . $warehouse->name,
            ]);
            $jobs = $productIds->chunk(self::CHUNK_SIZE)
                ->map(fn($chunk): \App\Jobs\SeedWarehouseStockBlock => new SeedWarehouseStockBlock(
                    $warehouse->id,
                    $warehouse->name,
                    $purchase->id,
                    $chunk->values()->all(),
                    self::STOCK_PER_PRODUCT,
                ))
                ->all();
            $batch = Bus::batch($jobs)
                ->name('inventory-seed-almacen-' . $warehouse->id)
                ->onQueue('inventory-seed')
                ->then(function (): void {
                    // Corre en el worker, no en esta consola: usar Log, no $this->info().
                })
                ->catch(function (Throwable $throwable) use ($warehouse): void {
                    Log::error('Batch de inventario fallido para almacén ' . $warehouse->id . ': ' . $throwable->getMessage());
                })
                ->dispatch();
            $this->info(sprintf(
                'Despachado batch %s para almacén "%s": %d bloques de %d productos',
                $batch->id,
                $warehouse->name,
                count($jobs),
                self::CHUNK_SIZE
            ));
        }

        $this->info('Listo. Para procesarlos: php artisan queue:work redis --queue=inventory-seed');
        $this->info('Para ver el progreso: SELECT name, total_jobs, pending_jobs, failed_jobs FROM job_batches;');

        return self::SUCCESS;
    }

    /**
     * Azure SQL Serverless se pausa cuando está inactivo y tarda unos
     * segundos en despertar. Este comando corre una sola vez (no es un job
     * con reintento automático de la cola), así que hay que esperar aquí
     * manualmente antes de la primera consulta real.
     */
    private function waitForDatabase(int $maxAttempts = 10): void
    {
        $delaysInSeconds = [3, 5, 8, 10, 15, 15, 20, 20, 30];
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                DB::select('select 1 as ping');
                return;
            } catch (Throwable $exception) {
                DB::disconnect();
                if ($attempt === $maxAttempts) {
                    throw $exception;
                }

                $wait = $delaysInSeconds[$attempt - 1] ?? 30;
                $this->warn(sprintf(
                    'Intento %d/%d: la base de datos no respondió (%s). Reintentando en %ds...',
                    $attempt,
                    $maxAttempts,
                    $exception->getMessage(),
                    $wait
                ));
                sleep($wait);
            }
        }
    }
}
