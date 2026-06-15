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
            ['name' => 'CAJA','abbreviation' => 'CJ','code' => '14'],
            ['name' => 'UNIDAD','abbreviation' => 'UD'],
            ['name' => 'DOCENA','abbreviation' => 'DC','code' => '15'],
        ];
        foreach ($units as $unit) {
            \App\Models\Unit::create([
                'name' => $unit['name'],
                'abbreviation' => $unit['abbreviation'],
                'code' => $unit['code'] ?? null,
            ]);
        }
    }
}
