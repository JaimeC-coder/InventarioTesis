<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        User::factory()->create([
            'name' => 'Eduardo Centurion',
            'email' => 'centurionjaime@gmail.com',
            'password' => bcrypt('admin123'),
        ]);
        $this->call([
            CategorySeeder::class,
            IdentitySeeder::class,
            WarehouseSeeder::class,
            ReasonSeeder::class,
            // Add other seeders here as needed
        ]);
        Product::factory(100)->create();
        Customer::factory(100)->create();
        Supplier::factory(100)->create();
    }
}
