<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirDryerCheck extends Model
{
    use HasFactory;

    protected $table = 'air_dryer_checks';

    protected $fillable = [
        'tanggal',
        'hari',
        'checked_by',  // Sekarang menyimpan nama checker
        'approved_by', // Bisa kosong (nullable)
    ];

    // Relasi ke AirDryerResult (tetap)
    public function results()
    {
        return $this->hasOne(AirDryerResult::class, 'check_id');
    }
}
