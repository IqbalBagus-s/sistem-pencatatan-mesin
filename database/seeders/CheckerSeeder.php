<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CheckerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('checkers')->insert([
            [
                'username' => 'UserC1',
                'password' => Hash::make('userc123'),
                'status' => 'aktif',
                'created_at' => '2025-05-07 15:28:06',
                'updated_at' => '2025-05-07 15:28:06',
                'deleted_at' => null,
            ],
            [
                'username' => 'UserC2',
                'password' => Hash::make('userc123'),
                'status' => 'aktif',
                'created_at' => '2025-05-07 15:28:06',
                'updated_at' => '2025-05-07 15:28:06',
                'deleted_at' => null,
            ],
            [
                'username' => 'Taufik',
                'password' => Hash::make('taufik123'),
                'status' => 'aktif',
                'created_at' => '2025-05-13 03:03:03',
                'updated_at' => '2025-05-13 03:03:03',
                'deleted_at' => null,
            ],
            [
                'username' => 'Ali',
                'password' => Hash::make('ali123'),
                'status' => 'aktif',
                'created_at' => '2025-05-13 03:03:04',
                'updated_at' => '2025-05-13 03:03:04',
                'deleted_at' => null,
            ],
            [
                'username' => 'Andrias',
                'password' => Hash::make('andrias123'),
                'status' => 'aktif',
                'created_at' => '2025-05-13 03:03:04',
                'updated_at' => '2025-05-13 03:03:04',
                'deleted_at' => null,
            ],
            [
                'username' => 'Dani',
                'password' => Hash::make('dani123'),
                'status' => 'aktif',
                'created_at' => '2025-05-13 03:03:05',
                'updated_at' => '2025-05-13 03:03:05',
                'deleted_at' => null,
            ],
            [
                'username' => 'Hartono',
                'password' => Hash::make('hartono123'),
                'status' => 'aktif',
                'created_at' => '2025-05-13 03:03:05',
                'updated_at' => '2025-05-13 03:03:05',
                'deleted_at' => null,
            ],
            [
                'username' => 'Vicky',
                'password' => Hash::make('vicky123'),
                'status' => 'aktif',
                'created_at' => '2025-05-13 03:03:06',
                'updated_at' => '2025-05-13 03:03:06',
                'deleted_at' => null,
            ],
            [
                'username' => 'Windra',
                'password' => Hash::make('windra123'),
                'status' => 'aktif',
                'created_at' => '2025-05-13 03:03:06',
                'updated_at' => '2025-05-13 03:03:06',
                'deleted_at' => null,
            ],
            [
                'username' => 'Rizky',
                'password' => Hash::make('rizky123'),
                'status' => 'aktif',
                'created_at' => '2025-05-13 03:03:07',
                'updated_at' => '2025-05-13 03:03:07',
                'deleted_at' => null,
            ],
            [
                'username' => 'Mahfud',
                'password' => Hash::make('mahfud123'),
                'status' => 'aktif',
                'created_at' => '2025-05-13 03:03:08',
                'updated_at' => '2025-05-13 03:03:08',
                'deleted_at' => null,
            ],
        ]);
    }
}