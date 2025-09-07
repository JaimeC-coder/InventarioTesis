<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->name(),
            'sku' => $this->faker->unique()->sentence(),
            'barcode' => $this->faker->unique()->numerify('##########'),
            'description' => $this->faker->paragraph(),
            'price_sale' => $this->faker->randomFloat(2, 1, 100),
            'price_purchase' => $this->faker->randomFloat(2, 1, 100),
            'category_id' => \App\Models\Category::all()->random()->id,
            'unit_id' => \App\Models\Unit::all()->random()->id,
            'measure_id' => \App\Models\Measure::all()->random()->id,
        ];
    }
}
