<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// Tambahkan import berikut
use Database\Seeders\CheckerSeeder;
use Database\Seeders\AdminSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // CheckerSeeder::class,
            AdminSeeder::class,
        ]);
    }
}
