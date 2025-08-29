<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reasons = [
            ['name' => 'Compra', 'type' => 1],
            ['name' => 'Venta', 'type' => 2],
            ['name' => 'Devolución de compra', 'type' => 2],
            ['name' => 'Devolución de venta', 'type' => 1],
            ['name' => 'Ajuste de inventario positivo', 'type' => 1],
            ['name' => 'Ajuste de inventario negativo', 'type' => 2],
            //['name' => 'Transferencia entre almacenes', 'type' => 0],
        ];
        foreach ($reasons as $reason) {
            \App\Models\Reason::create($reason);
        }
    }
}
