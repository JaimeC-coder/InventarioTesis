<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'ESCENCIAS',
                'description' => 'ESCENCIAS BASE , SOLUBLES y TURBIA',
            ],
            [
                'name' => 'COLORANTES',
                'description' => 'LIQUIDOS , GEL , A LA GRASA ,POLVO PARA PETALOS ,NACARADOS ,COLORANTE EX-A,COLORANTE EX-C',
            ],
            [
                'name' => 'MASAS',
                'description' => 'ELASTICA, FONDANT,DE COLORES,PASTA DE GOMA',
            ],
            [
                'name' => 'PRODUCTOS QUIMICOS',
                'description' => 'CARAMELINA,GLICERINA,GLUCOSA ,MANTENCA VEGETAL HIDROGENA , ETC',
            ],
        ];
        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'description' => $category['description'],
            ]);
        }
    }
}
