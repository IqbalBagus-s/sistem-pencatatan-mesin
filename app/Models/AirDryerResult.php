<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AirDryerResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'air_dryer_results';
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
    ];

    // Relasi ke AirDryerCheck
    public function airDryerCheck()
    {
        return $this->belongsTo(AirDryerCheck::class, 'check_id');
    }
}
