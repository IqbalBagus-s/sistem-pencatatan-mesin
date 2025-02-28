<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirDryerResult extends Model
{
    use HasFactory;

    protected $table = 'air_dryer_result';
    protected $fillable = [
        'check_id',
        'nomor_mesin',
        'temperatur_kompresor',
        'temperatur_kabel',
        'temperatur_mcb',
        'temperatur_angin_in',
        'temperatur_angin_out',
        'evaporator',
        'fan_evaporator',
        'auto_drain',
        'keterangan',
    ];

    // Relasi ke AirDryerCheck
    public function airDryerCheck()
    {
        return $this->belongsTo(AirDryerCheck::class, 'check_id');
    }
}
