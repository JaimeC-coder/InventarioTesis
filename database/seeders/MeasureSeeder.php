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
            ['name' => 'Galon', 'abbreviation' => 'gal', 'code' => '01'],
            ['name' => 'Kilo', 'abbreviation' => 'kg', 'code' => '02'],
            ['name' => 'Litro', 'abbreviation' => 'L', 'code' => '03'],
            ['name' => '1/2 Kilo', 'abbreviation' => '1/2 Kg', 'code' => '04','description_for_product' => '500 GR'],
            ['name' => '1/2 Litro', 'abbreviation' => '1/2 L', 'code' => '05','description_for_product' => '500 ML'],
            ['name' => '1/4 Kilo', 'abbreviation' => '1/4 Kg', 'code' => '06','description_for_product' => '250 GR'],
            ['name' => '1/4 Litro', 'abbreviation' => '1/4 L', 'code' => '07','description_for_product' => '250 ML'],
            ['name' => '90 Mililitros', 'abbreviation' => '90 ML', 'code' => '08'],
            ['name' => '90 Gramos', 'abbreviation' => '90 GR', 'code' => '09'],
            ['name' => 'Mililitros', 'abbreviation' => 'ML', 'code' => '10'],
            ['name' => '30 Mililitros', 'abbreviation' => '30 ML', 'code' => '11'],
            ['name' => '30 Gramos', 'abbreviation' => '30 GR', 'code' => '12'],
            ['name' => '10 Mililitros', 'abbreviation' => '10 ML', 'code' => '13'],
            ['name' => '10 Gramos', 'abbreviation' => '10 GR', 'code' => '14'],
            ['name' => '5 Kilos', 'abbreviation' => '5 Kg', 'code' => '15'],
            ['name' => '100 Gramos', 'abbreviation' => '100 GR', 'code' => '16'],
            ['name' => '50 Gramos', 'abbreviation' => '50 GR', 'code' => '17'],
            ['name' => '5 Gramos', 'abbreviation' => '5 GR', 'code' => '18'],
            ['name' => '2 Gramos', 'abbreviation' => '2 GR', 'code' => '19'],
            ['name' => 'Balde 25 Kilos', 'abbreviation' => 'Balde X 25 Kg', 'code' => '20'],
        ];
        foreach ($measures as $measure) {
            \App\Models\Measure::create([
                'name' => $measure['name'],
                'abbreviation' => $measure['abbreviation'],
                'code' => $measure['code'],
                'description_for_product' => 'X '.($measure['description_for_product'] ?? $measure['abbreviation']),
            ]);
        }
    }
}
