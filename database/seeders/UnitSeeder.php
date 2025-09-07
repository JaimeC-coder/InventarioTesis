<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'CAJA'],
            ['name' => 'UNIDAD'],
            ['name' => 'DOCENA']
        ];
        foreach ($units as $unit) {
            \App\Models\Unit::create([
                'name' => $unit['name'],
            ]);
        }
    }
}
