<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AirDryerCheck extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'air_dryer_checks';

    protected $fillable = [
        'tanggal',
        'hari',
        'checked_by',  // Sekarang menyimpan nama checker
        'approved_by', // Bisa kosong (nullable)
        'keterangan',
    ];

    // Relasi ke AirDryerResult (tetap)
    public function results()
    {
        return $this->hasOne(AirDryerResult::class, 'check_id');
    }
}
