<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterChillerResult extends Model
{
    use HasFactory;

    protected $table = 'water_chiller_results'; // Nama tabel

    protected $fillable = [
        'check_id',
        'checked_items',
        'CH1', 'CH2', 'CH3', 'CH4', 'CH5', 'CH6', 'CH7', 'CH8',
        'CH9', 'CH10', 'CH11', 'CH12', 'CH13', 'CH14', 'CH15', 'CH16',
        'CH17', 'CH18', 'CH19', 'CH20', 'CH21', 'CH22', 'CH23', 'CH24',
        'CH25', 'CH26', 'CH27', 'CH28', 'CH29', 'CH30', 'CH31', 'CH32',
    ];

    // Relasi ke WaterChillerCheck
    public function check()
    {
        return $this->belongsTo(WaterChillerCheck::class, 'check_id');
    }
}
