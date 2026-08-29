<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Supplier;
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
        \App\Models\Supplier::create([
            'document_number' => '20108832887',
            'identity' => 'RUC',
            'name' => 'Fratello S.A.C.',
            'email' => 'email@example.com',
            'phone' => '981268897',
            'address' => 'Jr. Jorge Chavez Nro. 351 (Mcdo de Breña)',
        ]);
        Customer::factory(100)->create();
        Supplier::factory(100)->create();
        $this->call([
            RolSeeder::class,
            CategorySeeder::class,
            WarehouseSeeder::class,
            ReasonSeeder::class,
            UnitSeeder::class,
            MeasureSeeder::class,
            // Add other seeders here as needed
        ]);
    }
}
