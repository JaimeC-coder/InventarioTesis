<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MeasureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $measures = [
            ['name' => 'Kilogram', 'abbreviation' => 'kg'],
            ['name' => 'Gram', 'abbreviation' => 'g'],
            ['name' => 'Liter', 'abbreviation' => 'L'],
            ['name' => 'Milliliter', 'abbreviation' => 'mL'],
            ['name' => 'Meter', 'abbreviation' => 'm'],
            ['name' => 'Centimeter', 'abbreviation' => 'cm'],
            ['name' => 'Piece', 'abbreviation' => 'pc'],
        ];
        foreach ($measures as $measure) {
            \App\Models\Measure::create([
                'name' => $measure['name'],
                'abbreviation' => $measure['abbreviation'],
            ]);
        }
    }
}
