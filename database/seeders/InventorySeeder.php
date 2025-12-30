<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use NumberFormatter;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subtotal = rand(1000, 5000);
        $totalimpuesto = $subtotal * 0.18;
        $total = $subtotal + $totalimpuesto;
        $numberFormatter = new NumberFormatter('es', NumberFormatter::SPELLOUT);
        $entero = floor($total);
        $decimal = round(($total - $entero) * 100);
        str_pad($decimal, 2, '0', STR_PAD_LEFT);
        ucfirst($numberFormatter->format($entero));
        $products = \App\Models\Product::where('stock', '>', 0)->get();
        $warehouses = \App\Models\Warehouse::all();
        $quotes = \App\Models\Purchase::latest()->first() ?? 0;
        $correlativo =  \App\Models\Purchase::where('serie', $quotes)->max('correlativo') ?? 0;
        $purchases = \App\Models\Purchase::create([
            'voucher_type' => 1,
            'serie' => 'OC-00001',
            'correlativo' =>  $correlativo + 1,
            'date' => now(),
            'supplier_id' => \App\Models\Supplier::first()->id,
            'warehouse_id' => \App\Models\Warehouse::first()->id,
            'status' => 'COMPLETADO',
            'subtotal' => $subtotal,
            'igv' => $totalimpuesto,
            'total' => $total,
            'total_string' => ucfirst($numberFormatter->format($entero)) . ' con ' . str_pad($decimal, 2, '0', STR_PAD_LEFT) . '/100',
            'user_id' => 11,
            'observation' => 'Initial stock seeder sale',
        ]);
        foreach ($products as $product) {
            foreach ($warehouses as $warehouse) {
                // Crear un registro de inventario
                $purchases->products()->attach($product->id, [
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
                $newQuantity = $lastQuantity + $product['stock'];
                $newTotal = $lastTotal + ($product['stock'] * $lastcostBalance);
                //$costBalance = $newQuantity > 0 ? $newTotal / $newQuantity : 0;
                $costBalance = $newTotal / ($newQuantity ?: 1);
                $purchases->inventories()->create([
                    'detail' => 'Compra ID: ' . $purchases->id,
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
