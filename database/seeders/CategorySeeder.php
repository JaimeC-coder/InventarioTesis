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
                'name' => 'Electronics',
                'description' => 'Devices and gadgets',
            ],
            [
                'name' => 'Furniture',
                'description' => 'Home and office furniture',
            ],
            [
                'name' => 'Clothing',
                'description' => 'Apparel and accessories',
            ],
        ];
        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'description' => $category['description']
            ]);
        }
    }
}
