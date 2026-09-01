<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        try {
            $this->call([
                // Database2Seeder::class,
                // InventorySeeder::class,
                registerInfoTest::class,
            ]);
        } catch (\Throwable $throwable) {
            dump($throwable->getMessage(), $throwable->getTraceAsString());
        }
    }
}
