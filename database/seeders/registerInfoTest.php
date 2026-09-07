<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\SalesCycleSimulator;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class registerInfoTest extends Seeder
{
    /**
     * Permite reanudar el seeder si se interrumpe a mitad de la corrida,
     * en vez de repetir el año completo desde cero.
     */
    private const CHECKPOINT_FILE = 'seeders/registerInfoTest_checkpoint.json';

    public function run(): void
    {
        $products = Product::where('is_active_product', 1)->get();
        $warehouses = Warehouse::all();
        $customerIds = Customer::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();
        $supplierId = Supplier::first()?->id ?? 1;
        if ($products->isEmpty() || $warehouses->isEmpty() || empty($customerIds) || empty($userIds)) {
            $this->command->error('Faltan datos base (products / warehouses / customers / users). Se aborta el seeder.');
            return;
        }

        $endOfYear = Carbon::create(2026, 10, 31, 23, 59, 59);
        $currentDate = $this->loadCheckpoint();
        if ($currentDate instanceof Carbon) {
            $this->command->info('Reanudando desde: ' . $currentDate->toDateString());
        } else {
            $currentDate = Carbon::create(2026, 1, 1, 9, 0, 0);
        }

        $simulator = new SalesCycleSimulator();
        $cycle = 1;
        while ($currentDate->lte($endOfYear)) {
            /** @var Warehouse $warehouse */
            $warehouse = $warehouses->random();
            Log::info(sprintf('Ciclo %d | Almacén: %s | Fecha: %s', $cycle, $warehouse->name, $currentDate->toDateString()));

            $simulator->runCycle($warehouse, $currentDate->copy(), $products, $customerIds, $userIds, $supplierId);

            $this->saveCheckpoint($currentDate);
            $currentDate->addDays(random_int(3, 4));
            $cycle++;
        }

        $this->clearCheckpoint();
        $this->command->info('Seeder de ciclos compra/venta 2026 completado exitosamente.');
    }

    private function loadCheckpoint(): ?Carbon
    {
        if (!Storage::exists(self::CHECKPOINT_FILE)) {
            return null;
        }

        $data = json_decode(Storage::get(self::CHECKPOINT_FILE), true);
        if (!isset($data['last_completed_date'])) {
            return null;
        }

        return Carbon::parse($data['last_completed_date'])->addDays(random_int(3, 4));
    }

    private function saveCheckpoint(Carbon $date): void
    {
        Storage::put(self::CHECKPOINT_FILE, json_encode(['last_completed_date' => $date->toDateString()]));
    }

    private function clearCheckpoint(): void
    {
        Storage::delete(self::CHECKPOINT_FILE);
    }
}
