<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompressorCheck extends Model
{
    use HasFactory;

    protected $table = 'compressor_checks'; // Nama tabel di database

    protected $fillable = [
        'tanggal',
        'hari',
        'checked_by_shift1',
        'checked_by_shift2',
        'approved_by_shift1',
        'approved_by_shift2',
        'kompressor_on_kl',
        'kompressor_on_kh',
        'mesin_on',
        'mesin_off',
        'temperatur_shift1',
        'tempertatur_shift2',
        'humidity_shift1',
        'humidity_shift2',
    ];

    /**
     * Relasi ke CompressorResult (One to Many)
     */
    public function results()
    {
        return $this->hasMany(CompressorResult::class, 'check_id');
    }
}
