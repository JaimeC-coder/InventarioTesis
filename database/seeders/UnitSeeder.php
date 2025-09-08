<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'CAJA','code' => '14'],
            ['name' => 'UNIDAD'],
            ['name' => 'DOCENA','code' => '15'],
        ];
        foreach ($units as $unit) {
            \App\Models\Unit::create([
                'name' => $unit['name'],
                'code' => $unit['code'] ?? null,
            ]);
        }
    }
}
