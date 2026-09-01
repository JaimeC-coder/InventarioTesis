<?php

namespace Database\Seeders;

use App\Enum\KardexTypeEnum;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Record;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\KardexServices;
use App\Services\ProductDetailServices;
use App\Services\UtilitisServices;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class registerInfoTest extends Seeder
{
    /**
     * ASUNCIONES (ajustar aquí si no calzan con tu negocio real):
     * - Cada ciclo (10 ventas + reposición) ocurre en UN solo almacén elegido
     *   al azar entre todos los existentes (no un almacén distinto por venta).
     * - Cada venta incluye entre 1 y 4 productos distintos, con cantidades
     *   entre 1 y 5 unidades por línea (limitado al stock disponible).
     * - "Faltante" = producto cuyo stock en Record llegó a 0 tras una venta.
     * - Cantidad a reponer = lo faltante + un extra aleatorio (hasta el doble
     *   de lo faltante), para evitar compras desproporcionadas.
     * - supplier_id fijo = el indicado (por defecto Supplier::first()->id, pero
     *   puedes forzarlo a 1 si tu Supplier con id=1 es el proveedor de prueba).
     */
    private const SALES_PER_CYCLE = 10;

    private const PURCHASES_PER_CYCLE = 2;

    private const PRODUCTS_PER_SALE_MIN = 1;

    private const PRODUCTS_PER_SALE_MAX = 15;

    private const QTY_PER_LINE_MIN = 1;

    private const QTY_PER_LINE_MAX = 10;

    private int $saleCorrelativo;

    private int $purchaseCorrelativo;

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

        $this->saleCorrelativo = (Sale::max('correlativo') ?? 0) + 1;
        $this->purchaseCorrelativo = (Purchase::max('correlativo') ?? 0) + 1;
        $currentDate = Carbon::create(2026, 1, 1, 9, 0, 0);
        $endOfYear = Carbon::create(2026, 12, 31, 23, 59, 59);
        DB::transaction(function () use ($products, $warehouses, $customerIds, $userIds, $supplierId, &$currentDate, $endOfYear): void {
            $cycle = 1;
            while ($currentDate->lte($endOfYear)) {
                /** @var Warehouse $warehouse */
                $warehouse = $warehouses->random();
                Log::info("Ciclo {$cycle} | Almacén: {$warehouse->name} | Fecha: {$currentDate->toDateString()}");
                $shortages = []; // [product_id => cantidad faltante acumulada]
                for ($i = 0; $i < self::SALES_PER_CYCLE; $i++) {
                    $this->createSale($products, $warehouse, $customerIds, $userIds, $currentDate->copy(), $shortages);
                }

                if (!empty($shortages)) {
                    $this->createReplenishmentPurchases($shortages, $products, $warehouse, $supplierId, $userIds, $currentDate->copy());
                }

                $currentDate->addDays(random_int(3, 4));
                $cycle++;
            }

            $this->command->info('Seeder de ciclos compra/venta 2026 completado exitosamente.');
        });
    }

    /**
     * Crea una venta con productos disponibles en el almacén dado y
     * registra en $shortages (por referencia) los productos que se
     * agotaron (quedaron en 0) tras esta venta.
     */
    private function createSale(
        Collection $products,
        Warehouse $warehouse,
        array $customerIds,
        array $userIds,
        Carbon $date,
        array &$shortages
    ): void {
        $available = $products->filter(fn($product) => $this->currentStock($product->id, $warehouse->id) > 0)->values();
        if ($available->isEmpty()) {
            return; // no hay nada que vender en este almacén ahora mismo
        }

        $count = min(random_int(self::PRODUCTS_PER_SALE_MIN, self::PRODUCTS_PER_SALE_MAX), $available->count());
        $selected = $available->random($count);
        $selected = $selected instanceof Collection ? $selected : collect([$selected]);
        $lines = [];
        foreach ($selected as $product) {
            $stock = $this->currentStock($product->id, $warehouse->id);
            if ($stock <= 0) {
                continue;
            }

            $qty = random_int(self::QTY_PER_LINE_MIN, min(self::QTY_PER_LINE_MAX, $stock));
            $price = (float) $product->price_sale_regular;
            $lines[] = [
                'id' => $product->id,
                'name' => $product->name,
                'quantity' => $qty,
                'price' => $price,
                'price_type' => 'GENERAL',
                'subtotal' => $qty * $price,
            ];
        }

        if (empty($lines)) {
            return;
        }

        $subtotal = collect($lines)->sum('subtotal');
        $total = $subtotal * 1.18;
        $sale = Sale::create([
            'voucher_type' => 2,
            'serie' => 'VN01',
            'correlativo' => $this->saleCorrelativo++,
            'date' => $date->toDateString(),
            'warehouse_id' => $warehouse->id,
            'customer_id' => $customerIds[array_rand($customerIds)],
            'quote_id' => null,
            'subtotal' => $subtotal,
            'igv' => $subtotal * 0.18,
            'total' => $total,
            'total_string' => UtilitisServices::TotalEnLetras($total, 'SOLES'),
            'observation' => 'Venta generada por seeder de ciclo compra/venta 2026',
            'payment_method' => 'EFECTIVO',
            'payment_type' => 'CONTADO',
            'user_id' => $userIds[array_rand($userIds)],
        ]);
        // Alinear timestamps con la fecha simulada del ciclo
        $sale->forceFill(['created_at' => $date, 'updated_at' => $date])->saveQuietly();
        ProductDetailServices::createDetailproductableExit($sale, $lines, $warehouse->id, 'Venta seeder ID: ' . $sale->id);
        foreach ($lines as $line) {
            $remaining = $this->currentStock($line['id'], $warehouse->id);
            if ($remaining <= 0) {
                $shortages[$line['id']] = ($shortages[$line['id']] ?? 0) + $line['quantity'];
            }
        }
    }

    /**
     * Reparte los productos faltantes entre exactamente 2 compras de reposición.
     */
    private function createReplenishmentPurchases(
        array $shortages,
        Collection $products,
        Warehouse $warehouse,
        int $supplierId,
        array $userIds,
        Carbon $date
    ): void {
        $productIds = array_keys($shortages);
        shuffle($productIds);
        $chunks = array_chunk($productIds, (int) max(1, ceil(count($productIds) / self::PURCHASES_PER_CYCLE)));
        foreach (array_slice($chunks, 0, self::PURCHASES_PER_CYCLE) as $chunk) {
            $lines = [];
            foreach ($chunk as $productId) {
                $product = $products->firstWhere('id', $productId);
                if (!$product) {
                    continue;
                }

                $needed = $shortages[$productId];
                // Reponer un valor aleatorio mayor a lo necesitado, sin exagerar
                $qty = $needed + random_int(1, max(1, $needed * 2));
                $price = (float) $product->price_purchase;
                $lines[] = [
                    'id' => $product->id,
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $qty * $price,
                    'product_name' => $product->name,
                    'price_type' => 'COMPRA',
                ];
            }

            if (empty($lines)) {
                continue;
            }

            $subtotal = collect($lines)->sum('subtotal');
            $igv = $subtotal * 0.18;
            $total = $subtotal + $igv;
            $purchase = Purchase::create([
                'voucher_type' => 1,
                'serie' => 'CM01',
                'correlativo' => $this->purchaseCorrelativo++,
                'date' => $date->toDateString(),
                'supplier_id' => $supplierId,
                'warehouse_id' => $warehouse->id,
                'status' => 'RECIBIDO',
                'subtotal' => $subtotal,
                'igv' => $igv,
                'total' => $total,
                'total_string' => UtilitisServices::TotalEnLetras($total, 'SOLES'),
                'user_id' => $userIds[array_rand($userIds)],
                'observation' => 'Compra de reposición generada por seeder de ciclo 2026 - Almacén: ' . $warehouse->name,
            ]);
            $purchase->forceFill(['created_at' => $date, 'updated_at' => $date])->saveQuietly();
            foreach ($lines as $line) {
                $purchase->products()->attach($line['id'], [
                    'quantity' => $line['quantity'],
                    'price' => $line['price'],
                    'subtotal' => $line['subtotal'],
                    'product_name' => $line['product_name'],
                    'price_type' => $line['price_type'],
                ]);
                // attach() por sí solo NO mueve stock: hay que registrar el
                // movimiento en Inventorie explícitamente (dispara el Observer).
                KardexServices::registerEntry(
                    $purchase,
                    ['id' => $line['id'], 'name' => $line['product_name'], 'quantity' => $line['quantity']],
                    $warehouse->id,
                    'Compra de reposición seeder ID: ' . $purchase->id,
                    KardexTypeEnum::ENTRADA
                );
            }
        }
    }

    /**
     * Stock actual de un producto en un almacén, según el resumen en Record.
     */
    private function currentStock(int $productId, int $warehouseId): int
    {
        return (int) (Record::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->value('quantity') ?? 0);
    }
}
