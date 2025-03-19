<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WaterChillerResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'water_chiller_results'; // Nama tabel

    protected $fillable = [
        'check_id',
        'no_mesin',
        'Temperatur_Compressor',
        'Temperatur_Kabel',
        'Temperatur_Mcb',
        'Temperatur_Air',
        'Temperatur_Pompa',
        'Evaporator',
        'Fan_Evaporator',
        'Freon',
        'Air',
    ];

    // Relasi ke WaterChillerCheck
    public function check()
    {
        return $this->belongsTo(WaterChillerCheck::class, 'check_id');
    }
}
