<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Host;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Host::create([
            'username' => 'host',
            'password' => Hash::make('aaspra123'), // bcrypt password
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
