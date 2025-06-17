<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ApproverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('approvers')->insert([
            [
                'username' => 'Approver1',
                'password' => Hash::make('approver1123'),
                'role' => 'Penanggung Jawab',
                'status' => 'aktif',
                'created_at' => '2025-05-08 07:15:33',
                'updated_at' => '2025-05-08 07:15:33',
                'deleted_at' => null,
            ],
            [
                'username' => 'Dewi',
                'password' => Hash::make('dewi123'),
                'role' => 'Penanggung Jawab',
                'status' => 'aktif',
                'created_at' => '2025-05-13 03:03:03',
                'updated_at' => '2025-05-13 03:03:03',
                'deleted_at' => null,
            ],
            [
                'username' => 'Abdul',
                'password' => Hash::make('abdul123'),
                'role' => 'Penanggung Jawab',
                'status' => 'aktif',
                'created_at' => '2025-05-13 03:46:11',
                'updated_at' => '2025-05-13 03:46:11',
                'deleted_at' => null,
            ],
            [
                'username' => 'Bismar',
                'password' => Hash::make('bismar123'),
                'role' => 'Penanggung Jawab',
                'status' => 'aktif',
                'created_at' => '2025-05-13 03:48:56',
                'updated_at' => '2025-05-13 03:48:56',
                'deleted_at' => null,
            ],
            [
                'username' => 'IT',
                'password' => Hash::make('it123'),
                'role' => 'Penanggung Jawab',
                'status' => 'aktif',
                'created_at' => '2025-05-13 08:20:02',
                'updated_at' => '2025-05-13 08:20:02',
                'deleted_at' => null,
            ]
        ]);
    }
}