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
                'name' => 'ESCENCIAS BASES',
                'codigo' => 10,
                'description' => 'ESC. BASE',
            ],
            [
                'name' => 'ESCENCIAS SOLUBLES',
                'codigo' => 20,
                'description' => 'ESC. SOLUBLE',
            ],
            [
                'name' => 'ESCENCIAS TURBIAS',
                'codigo' => 30,
                'description' => 'ESC. TURBIA',
            ],
            [
                'name' => 'COLORANTES LIQUIDOS',
                'codigo' => 40,
                'description' => 'COL. LIQUIDO',
            ],
            [
                'name' => 'COLORANTES A LA GRASA',
                'codigo' => 50,
                'description' => 'COL. A LA GRASA',
            ],
            [
                'name' => 'COLORANTES EX-A',
                'codigo' => 60,
                'description' => 'COL. EX-A',
            ],
            [
                'name' => 'COLORANTES EX-B',
                'codigo' => 61,
                'description' => 'COL. EX-B',
            ],
            [
                'name' => 'COLORANTES EX-C',
                'codigo' => 62,
                'description' => 'COL. EX-C',
            ],
            [
                'name' => 'COLORANTES DE PETALOS',
                'codigo' => 70,
                'description' => 'COL. DE PETALO',
            ],
            [
                'name' => 'NACARADOS',
                'codigo' => 80,
                'description' => 'NACARADO',
            ],
            [
                'name' => 'COLORANTES EN GEL',
                'codigo' => 90,
                'description' => 'COL. EN GEL',
            ],
            [
                'name' => 'COLORANTES EN PASTA',
                'codigo' => 91,
                'description' => 'COL. EN PASTA',
            ],
            [
                'name' => 'MASAS',
                'codigo' => 100,
                'description' => 'MASAS ELASTICAS ,PASTA DE GOMA Y MASA FONDANT',
            ],
            [
                'name' => 'PRODUCUTOS QUIMICOS 1',
                'codigo' => 101,
                'description' => 'AZUCAR IMPALPABLE , POLVO PARA HORNO, COLOREX',
            ],
            [
                'name' => 'PRODUCUTOS QUIMICOS 2',
                'codigo' => 102,
                'description' => 'CARAMELINA,GLICERINA,GLUCOSA ,MANTENCA VEGETAL HIDROGENA , ETC',
            ],
        ];
        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'codigo' => $category['codigo'],
                'description' => $category['description'],
            ]);
        }
    }
}
