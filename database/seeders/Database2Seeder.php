<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class Database2Seeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(10)->create();
        $user = \App\Models\User::factory()->create([
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
        \App\Models\Customer::factory(100)->create();
        \App\Models\Supplier::factory(100)->create();
        $this->call([
            RolSeeder::class,
            CategorySeeder::class,
            WarehouseSeeder::class,
            ReasonSeeder::class,
            UnitSeeder::class,
            MeasureSeeder::class,
            // Add other seeders here as needed
        ]);
        \App\Models\Employee::create([
            'document' => '12345678',
            'phone' => '987654321',
            'address' => 'Calle Falsa 123',
            'fechaNacimiento' => '1990-01-01',
            'user_id' => $user->id,
        ]);
        $user->assignRole('Administrador');
    }
}
