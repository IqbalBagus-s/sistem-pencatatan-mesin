<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VacumCleanerCheck extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vacum_cleaner_checks';

    protected $fillable = [
        'nomer_vacum_cleaner',
        'bulan',
        'tanggal_dibuat',
        'checker_minggu1',
        'checker_minggu2',
        'approver_minggu1',
        'approver_minggu2',
    ];

    /**
     * Mendapatkan hasil pemeriksaan untuk tabel 1 (minggu 1-2)
     */
    public function hasilTable1()
    {
        return $this->hasMany(VacumCleanerResultsTable1::class, 'check_id');
    }

    /**
     * Mendapatkan hasil pemeriksaan untuk tabel 2 (minggu 3-4)
     */
    public function hasilTable2()
    {
        return $this->hasMany(VacumCleanerResultsTable2::class, 'check_id');
    }
}
