<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $forms = [
            // 1
            [
                'nomor_form' => 'APTEK/006/REV.01',
                'nama_form' => 'PENGECEKAN VACUUM CLEANER',
                'tanggal_efektif' => '2023-07-14 00:00:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 2
            [
                'nomor_form' => 'APTEK/014/REV.00',
                'nama_form' => 'PENGECEKAN MESIN SLEETING',
                'tanggal_efektif' => '2022-11-29 00:00:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 3
            [
                'nomor_form' => 'APTEK/048/REV.01',
                'nama_form' => 'CHECKLIST DEHUM MATRAS',
                'tanggal_efektif' => '2023-07-14 00:00:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 4
            [
                'nomor_form' => 'APTEK/019/REV.02',
                'nama_form' => 'PENGECEKAN MESIN AIR DRYER',
                'tanggal_efektif' => '2024-06-19 00:00:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 5
            [
                'nomor_form' => 'APTEK/023/REV.01',
                'nama_form' => 'PENGECEKAN CHILLER',
                'tanggal_efektif' => '2022-11-29 00:00:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 6
            [
                'nomor_form' => 'APTEK/015/REV.04',
                'nama_form' => 'PEMERIKSAAN MESIN GILING',
                'tanggal_efektif' => '2023-07-05 00:00:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 7
            [
                'nomor_form' => 'APTEK/047/REV.01',
                'nama_form' => 'PENGECEKAN HOPPER',
                'tanggal_efektif' => '2023-02-24 00:00:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 8
            [
                'nomor_form' => 'APTEK/035/REV.02',
                'nama_form' => 'PENGECEKAN DEHUM BAHAN',
                'tanggal_efektif' => '2023-05-17 00:00:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 9
            [
                'nomor_form' => 'APTEK/041/REV.00',
                'nama_form' => 'FORM PEMERIKSAAN COMPRESSOR',
                'tanggal_efektif' => '2022-11-29 00:00:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 10
            [
                'nomor_form' => 'APTEK/046/REV.01',
                'nama_form' => 'FORM PENGECEKAN AUTOLOADER',
                'tanggal_efektif' => '2023-07-14 00:00:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 11
            [
                'nomor_form' => 'APTEK/016/REV.01',
                'nama_form' => 'FORM PENGECEKAN MESIN CAPLINING',
                'tanggal_efektif' => '2022-10-19 00:00:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 12
            [
                'nomor_form' => 'APTEK/005/REV.00',
                'nama_form' => 'CHECKLIST PERAWATAN CRANE MATRAS',
                'tanggal_efektif' => '2022-11-29 00:00:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('forms')->insert($forms);
    }
}