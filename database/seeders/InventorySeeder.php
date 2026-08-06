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
        $correlativo = \App\Models\Purchase::latest()->first() ?? 0;
        $correlativo =  \App\Models\Purchase::where('correlativo', $correlativo)->max('correlativo') ?? 0;

        $purchases = \App\Models\Purchase::create([
            'voucher_type' => 1,
            'serie' => 'C001',
            'correlativo' =>  $correlativo + 1,
            'date' => now(),
            'supplier_id' => \App\Models\Supplier::first()->id,
            'warehouse_id' => \App\Models\Warehouse::first()->id,
            'status' => 'RECIBIDO',
            'subtotal' => $subtotal,
            'igv' => $totalimpuesto,
            'total' => $total,
            'total_string' => ucfirst($numberFormatter->format($entero)) . ' con ' . str_pad($decimal, 2, '0', STR_PAD_LEFT) . '/100',
            'user_id' => 11,
            'observation' => 'Initial stock seeder sale',
        ]);
        foreach ($products as $product) {
            foreach ($warehouses as $warehouse) {
                // Crear el detalle del compra
                $purchases->products()->attach($product->id, [
                    'quantity' => $product->stock,
                    'price' => $product->price_purchase,
                    'subtotal' => $product->stock * $product->price_purchase,
                    'product_name' => $product->name,
                    'price_type' => 'COMPRA',
                ]);
                $purchase_in = $purchases->inventories()->where('product_id', $product->id)->where('warehouse_id', $warehouse->id)->sum('quantity_in');
                $purchase_out = $purchases->inventories()->where('product_id', $product->id)->where('warehouse_id', $warehouse->id)->sum('quantity_out');
                $purchases->inventories()->create([
                    'detail' => 'Compra ID: ' . $purchases->id,
                    'quantity_in' => $product['stock'],
                    'quantity_total' => $purchase_in - $purchase_out + $product['stock'],
                    'product_name' => $product->name,
                    'type' => 'Entrada',
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                ]);
            }
        }
    }
}
