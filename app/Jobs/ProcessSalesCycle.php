<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\SalesCycleSimulator;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessSalesCycle implements ShouldQueue
{
    use Batchable;

    use Dispatchable;

    use InteractsWithQueue;

    use Queueable;

    use SerializesModels;

    public int $tries = 10;

    public int $timeout = 120;

    public function __construct(
        public readonly int $cycleNumber,
        public readonly string $date,
    ) {
    }

    /**
     * Espera creciente entre reintentos automáticos del job completo.
     */
    public function backoff(): array
    {
        return [5, 10, 15, 20, 30, 30, 30, 30, 30];
    }

    public function handle(): void
    {
        try {
            // "Ping" para forzar que Azure SQL Serverless despierte de su
            // auto-pause antes de intentar el ciclo real.
            DB::select('select 1 as ping');
        } catch (Throwable $throwable) {
            // El worker es un proceso de larga duración: si la conexión se
            // cortó, hay que forzar una reconexión antes del próximo intento.
            DB::disconnect();
            throw $throwable;
        }

        $products = Product::where('is_active_product', 1)->get();
        $warehouses = Warehouse::all();
        $customerIds = Customer::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();
        $supplierId = Supplier::first()?->id ?? 1;
        if ($products->isEmpty() || $warehouses->isEmpty() || $customerIds === [] || $userIds === []) {
            Log::error('ProcessSalesCycle: faltan datos base (products/warehouses/customers/users). Ciclo ' . $this->cycleNumber . ' omitido.');
            return;
        }

        /** @var Warehouse $warehouse */
        $warehouse = $warehouses->random();
        Log::info(sprintf('Ciclo %d | Almacén: %s | Fecha: %s', $this->cycleNumber, $warehouse->name, $this->date));
        (new SalesCycleSimulator())->runCycle(
            $warehouse,
            Carbon::parse($this->date),
            $products,
            $customerIds,
            $userIds,
            $supplierId
        );
    }

    public function failed(?Throwable $throwable): void
    {
        Log::error(sprintf(
            'ProcessSalesCycle agotó los %d intentos. Ciclo %d, fecha %s. Error: %s',
            $this->tries,
            $this->cycleNumber,
            $this->date,
            $throwable?->getMessage() ?? 'desconocido'
        ));
    }
}
