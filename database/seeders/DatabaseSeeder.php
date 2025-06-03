<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Database\Seeders\ApproverSeeder;
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
            ApproverSeeder::class,
            CheckerSeeder::class,
            HostSeeder::class,
            FormSeeder::class,
        ]);
    }
}
