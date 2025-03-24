<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HopperCheck extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hopper_checks';

    protected $fillable = [
        'nomer_hopper',
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
     * Relasi dengan tabel hopper_results (one-to-many).
     */
    public function results()
    {
        return $this->hasMany(HopperResult::class, 'check_id');
    }
}
