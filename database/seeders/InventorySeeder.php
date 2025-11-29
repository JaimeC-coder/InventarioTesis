<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Si yo registro un producto nuevo con stock incial debo crear un registro en inventarios y ademas debo crear un registro en el kardex
        // ademas de una registro en la tabla productables para relacionar el producto con el inventario
        // sin embargo, en este seeder solo crearemos registros de inventarios y kardex
        // para productos que ya existen en la base de datos en la tabla de products ya tengo tanto un precio de compra como un stock inicial
        // Ademas quiero que este nuevo ingreso se relacione como si fueran compras nuevas de inventario
        // Todos como si fueran una sola compra inicial
        $products = \App\Models\Product::where('stock', '>', 0)->get();
        $warehouses = \App\Models\Warehouse::all();
        $quotes = \App\Models\Quote::latest()->first() ?? 0 ;
        $correlativo =  \App\Models\Quote::where('serie', $quotes)->max('correlativo') ?? 0;
        // Log::info($correlativo);
        // die();
        $sale = \App\Models\Sale::create([
            'voucher_type' => 1,
            'serie' => 'OC-00000',
            'correlativo' =>  $correlativo + 1,
            'date' => now(),
            'customer_id' => \App\Models\Customer::first()->id,
            'warehouse_id' => \App\Models\Warehouse::first()->id,
            'total' => 0,
            'observation' => 'Initial stock seeder sale',
        ]);
        foreach ($products as $product) {
            foreach ($warehouses as $warehouse) {
                // Crear un registro de inventario
                $sale->products()->attach($product->id, [
                    'quantity' => $product->stock,
                    'price' => $product->price_purchase,
                    'subtotal' => $product->stock * $product->price_purchase,
                ]);
                $lastrecortd = \App\Models\Inventorie::where('product_id', $product->id)
                    ->where('warehouse_id', $warehouse->id)
                    ->latest()
                    ->first();
                $lastQuantity = $lastrecortd ? $lastrecortd->quantity_balance : 0;
                $lastTotal = $lastrecortd ? $lastrecortd->total_balance : 0;
                $lastcostBalance = $lastrecortd ? $lastrecortd->cost_balance : 0;
                $newQuantity = $lastQuantity - $product['stock'];
                $newTotal = $lastTotal - ($product['stock'] * $lastcostBalance);
                //$costBalance = $newQuantity > 0 ? $newTotal / $newQuantity : 0;
                $costBalance = $newTotal / ($newQuantity ?: 1);
                $sale->inventories()->create([
                    'detail' => 'Venta ID: ' . $sale->id,
                    'cost_out' => $lastcostBalance,
                    'total_out' => $product['stock'] * $lastcostBalance,
                    'quantity_out' => $product['stock'],
                    'quantity_balance' => $newQuantity,
                    'cost_balance' => $costBalance,
                    'total_balance' => $newTotal,
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                ]);
            }
        }
    }
}
