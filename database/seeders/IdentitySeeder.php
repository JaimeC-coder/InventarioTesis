<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IdentitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $identities = [
            ['name'=> 'Sin Identidad'],
            ['name' => 'DNI'],
            ['name' => 'RUC'],
            ['name' => 'Pasaporte'],
            ['name' => 'Carnet de extranjería'],
        ];

        foreach ($identities as $identity) {
            \App\Models\Identity::create([
                'name' => $identity['name'],
            ]);
        }


    }
}
