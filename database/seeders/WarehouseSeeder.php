<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = [
            ['name' => 'Almacén Central',  'location' => 'Calle Principal 123'],
            ['name' => 'Almacén Secundario',  'location' => 'Avenida Secundaria 456'],
            ['name' => 'Almacén de Productos Peligrosos',  'location' => 'Zona Industrial 789'],
        ];

        foreach ($warehouses as $warehouse) {
            \App\Models\Warehouse::create([
                'name' => $warehouse['name'],
                'location' => $warehouse['location'],

            ]);
        }
    }
}
