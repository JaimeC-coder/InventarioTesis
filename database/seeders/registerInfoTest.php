<?php

namespace Database\Seeders;

use App\Services\KardexServices;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log as FacadesLog;
use NumberFormatter;

class registerInfoTest extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $randomSales = rand(1, 100);
        FacadesLog::info('Número de ventas a generar: ' . $randomSales);
        for ($j = 0; $j < $randomSales; $j++) {
            $randonmProduct = rand(1, 30);
            $sumador = 0;
            $products = \App\Models\Product::where('stock', '>', 0)->get();
            $warehouses = \App\Models\Warehouse::inRandomOrder()->first()->id;
            $quotes = \App\Models\Sale::latest()->first() ?? 0;
            $correlativo =  \App\Models\Sale::where('serie', $quotes)->max('correlativo') ?? 0;
            $sales = \App\Models\Sale::create([
                'voucher_type' => 1,
                'serie' => 'VN-00001',
                'correlativo' =>  $correlativo + 1,
                'date' => now(),
                'customer_id' => \App\Models\Customer::first()->id,
                'warehouse_id' => \App\Models\Warehouse::first()->id,
                'currency' => 'SOLES',
                'status' => 'COMPLETADO',
                'payment_method' => 'EFECTIVO',
                'payment_type' => 'CONTADO',
                'subtotal' => 0,
                'igv' => 0,
                'total' => 0,
                'total_string' => 'cero',
                'user_id' => 11,
                'observation' => 'Initial stock seeder sale',
            ]);
            for ($i = 0; $i < $randonmProduct; $i++) {
                // Crear un registro de venta
                $cuantity = rand(1, 5);
                $product = $products->random();
                FacadesLog::info('Producto seleccionado: ' . $product->name . ' con stock: ' . $product->stock . ' y precio a vender: ' . $product->price_sale_regular);
                $sales->products()->attach($product->id, [
                    'price_type' => 'NONE',
                    'quantity' => $cuantity,
                    'price' => $product->price_sale_regular,
                    'subtotal' => $product->price_sale_regular * $cuantity,
                ]);
                $sumador += $product->price_sale_regular * $cuantity;
                //quiero transformar el producto en un array para pasarlo a la función de kardex
                $productTemp = $product->id;
                $product = $product->toArray();
                $product['id'] = $productTemp;
                $product['quantity'] = $cuantity;
                FacadesLog::info('Producto seleccionado: ', ['product' => $product]);
                KardexServices::registerExit($sales, $product, $warehouses, 'Venta ID: ' . $sales->id);
            }

            $totalimpuesto = $sumador * 0.18;
            $total = $sumador + $totalimpuesto;
            $numberFormatter = new NumberFormatter('es', NumberFormatter::SPELLOUT);
            $entero = floor($total);
            $decimal = round(($total - $entero) * 100);
            str_pad($decimal, 2, '0', STR_PAD_LEFT);
            ucfirst($numberFormatter->format($entero));
            $sales->update([
                'subtotal' => $sumador,
                'igv' => $totalimpuesto,
                'total' => $total,
                'total_string' => ucfirst($numberFormatter->format($entero)) . ' con ' . str_pad($decimal, 2, '0', STR_PAD_LEFT) . '/100',
            ]);
        }
    }
}
