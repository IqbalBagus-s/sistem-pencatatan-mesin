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
                'username' => 'jeri',
                'password' => Hash::make('123456'), // bcrypt password
                'role' => 'Penanggung Jawab',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'username' => 'samul',
                'password' => Hash::make('123456'), // bcrypt password
                'role' => 'Penanggung Jawab',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]
        ]);
    }
}