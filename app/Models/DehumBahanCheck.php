<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DehumBahanCheck extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dehum_bahan_checks';

    protected $fillable = [
        'nomer_dehum_bahan',
        'bulan',
        // Data tanggal tiap minggu
        'tanggal_minggu1',
        'tanggal_minggu2',
        'tanggal_minggu3',
        'tanggal_minggu4',
        // Data checker tiap minggu
        'checked_by_minggu1',
        'checked_by_minggu2',
        'checked_by_minggu3',
        'checked_by_minggu4',
        // Data approver tiap minggu
        'approved_by_minggu1',
        'approved_by_minggu2',
        'approved_by_minggu3',
        'approved_by_minggu4',
    ];

    /**
     * Relasi dengan tabel dehum_bahan_results (one-to-many).
     */
    public function results()
    {
        return $this->hasMany(DehumBahanResult::class, 'check_id');
    }
}
