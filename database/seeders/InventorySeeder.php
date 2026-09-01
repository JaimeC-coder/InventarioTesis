<?php

namespace Database\Seeders;

use App\Enum\KardexTypeEnum;
use App\Services\ProductDetailServices;
use App\Services\UtilitisServices;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = \App\Models\Product::where('is_active_product', 1)->get();
        $warehouses = \App\Models\Warehouse::all();
        $stok = 100;
        DB::transaction(function () use ($products, $warehouses, $stok): void {
            foreach ($warehouses as $warehouse) {
                Log::info('Seeding initial stock for warehouse ID: ' . $warehouse->id . ' - ' . $warehouse->name);
                $correlativo = (\App\Models\Purchase::max('correlativo') ?? 0) + 1;
                $subtotal = 0;
                $purchases = \App\Models\Purchase::create([
                    'voucher_type' => 1,
                    'serie' => 'CM01',
                    'correlativo' =>  $correlativo,
                    'date' => Carbon::parse('2025-12-15')->format('Y-m-d'),
                    'supplier_id' => \App\Models\Supplier::first()->id, //fratello
                    'warehouse_id' => $warehouse->id,
                    'status' => 'RECIBIDO',
                    'subtotal' => 0,
                    'igv' => 0,
                    'total' => 00,
                    'total_string' => 'cero con 00/100',
                    'user_id' => 11,
                    'observation' => 'Initial stock seeder almacen ID: ' . $warehouse->id . ' - ' . $warehouse->name,
                ]);
                // ProductDetailServices::createDetailproductableOrdenCompra($purchases, $products);
                foreach ($products as $product) {
                    $purchases->products()->attach($product->id, [
                        'quantity' => $stok,
                        'price' => $product->price_purchase,
                        'subtotal' => $stok * $product->price_purchase,
                        'product_name' => $product->name,
                        'price_type' => 'COMPRA',
                    ]);
                    $subtotal += $stok * $product->price_purchase;
                    \App\Services\KardexServices::registerEntry(
                        $purchases,
                        ['id' => $product->id, 'name' => $product->name, 'quantity' => $stok],
                        $warehouse->id,
                        'Initial stock seeder almacen ID: ' . $warehouse->id . ' - ' . $warehouse->name,
                        KardexTypeEnum::ENTRADA
                    );
                }

                $igv = $subtotal * 0.18;
                $total = $subtotal + $igv;
                $purchases->update([
                    'subtotal' => $subtotal,
                    'igv' => $igv,
                    'total' => $total,
                    'total_string' => UtilitisServices::TotalEnLetras($total, 'SOLES'),
                ]);
            }

            $this->command->info('Seeder completado exitosamente.');
        });
    }
}
