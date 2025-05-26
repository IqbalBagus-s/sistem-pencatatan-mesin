<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// Tambahkan import berikut
use Database\Seeders\CheckerSeeder;
use Database\Seeders\HostSeeder;
use Database\Seeders\FormSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CheckerSeeder::class,
            HostSeeder::class,
            FormSeeder::class,
        ]);
    }
}
