<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Supplier;
use App\Models\TypeCustomer;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Database2Seeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();
        User::factory()->create([
            'name' => 'Eduardo Centurion',
            'email' => 'centurionjaime@gmail.com',
            'password' => bcrypt('admin123'),
        ]);
        TypeCustomer::create(
            ['type' => 'GENERAL', 'porcentage_discount' => 0.00000]
        );
        TypeCustomer::create(
            ['type' => 'A1', 'porcentage_discount' => 0.500000]
        );
        \App\Models\Identity::create([
            'name' => 'Sin Identidad',
        ]);
        \App\Models\Identity::create([
            'name' => 'DNI',
        ]);
        \App\Models\Identity::create([
            'name' => 'RUC',
        ]);
        \App\Models\Identity::create([
            'name' => 'Pasaporte',
        ]);
        \App\Models\Identity::create([
            'name' => 'Carnet de extranjería',
        ]);
        Customer::factory(100)->create();
        Supplier::factory(100)->create();
        $this->call([
            CategorySeeder::class,
            WarehouseSeeder::class,
            ReasonSeeder::class,
            UnitSeeder::class,
            MeasureSeeder::class,
            // Add other seeders here as needed
        ]);
    }
}
