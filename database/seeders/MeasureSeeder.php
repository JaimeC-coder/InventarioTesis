<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MeasureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { //description_for_product
        $measures = [
            ['name' => 'Galon', 'abbreviation' => 'gal', 'code' => '01', 'category' => 'LIQUIDO'],
            ['name' => 'Kilo', 'abbreviation' => 'kg', 'code' => '02', 'category' => 'PESO'],
            ['name' => 'Litro', 'abbreviation' => 'L', 'code' => '02', 'category' => 'LIQUIDO'],
            ['name' => '1/2 Kilo', 'abbreviation' => '1/2 Kg', 'code' => '03', 'description_for_product' => '500 GR', 'category' => 'PESO'],
            ['name' => '1/2 Litro', 'abbreviation' => '1/2 L', 'code' => '03', 'description_for_product' => '500 ML', 'category' => 'LIQUIDO'],
            ['name' => '1/4 Kilo', 'abbreviation' => '1/4 Kg', 'code' => '04', 'description_for_product' => '250 GR', 'category' => 'PESO'],
            ['name' => '1/4 Litro', 'abbreviation' => '1/4 L', 'code' => '04', 'description_for_product' => '250 ML', 'category' => 'LIQUIDO'],
            ['name' => '90 Mililitros', 'abbreviation' => '90 ML', 'code' => '05', 'category' => 'LIQUIDO'],
            ['name' => '90 Gramos', 'abbreviation' => '90 GR', 'code' => '05', 'category' => 'PESO'],
            // ['name' => 'Mililitros', 'abbreviation' => 'ML', 'code' => '10'],
            ['name' => '30 Mililitros', 'abbreviation' => '30 ML', 'code' => '06', 'category' => 'LIQUIDO'],
            ['name' => '30 Gramos', 'abbreviation' => '30 GR', 'code' => '06', 'category' => 'PESO'],
            ['name' => '10 Mililitros', 'abbreviation' => '10 ML', 'code' => '07', 'category' => 'LIQUIDO'],
            ['name' => '10 Gramos', 'abbreviation' => '10 GR', 'code' => '07', 'category' => 'PESO'],
            ['name' => '5 Kilos', 'abbreviation' => '5 Kg', 'code' => '08', 'category' => 'PESO'],
            ['name' => '100 Gramos', 'abbreviation' => '100 GR', 'code' => '09', 'category' => 'PESO'],
            ['name' => '50 Gramos', 'abbreviation' => '50 GR', 'code' => '10' , 'category' => 'PESO'],
            ['name' => '5 Gramos', 'abbreviation' => '5 GR', 'code' => '11', 'category' => 'PESO'],
            ['name' => '2 Gramos', 'abbreviation' => '2 GR', 'code' => '12', 'category' => 'PESO'],
            ['name' => 'Balde 25 Kilos', 'abbreviation' => 'Balde X 25 Kg', 'code' => '13', 'category' => 'PESO'],
        ];
        foreach ($measures as $measure) {
            \App\Models\Measure::create([
                'name' => $measure['name'],
                'abbreviation' => $measure['abbreviation'],
                'code' => $measure['code'],
                'description_for_product' => 'X ' . ($measure['description_for_product'] ?? $measure['abbreviation']),
                'category' => $measure['category'],
            ]);
        }
    }
}
