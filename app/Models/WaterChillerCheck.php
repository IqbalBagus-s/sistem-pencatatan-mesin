<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WaterChillerCheck extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'water_chiller_checks'; // Nama tabel

    protected $fillable = [
        'tanggal',
        'hari',
        'checked_by',
        'approved_by',
        'keterangan',
    ];
    
    // Relasi antar table
    public function results()
    {
        return $this->hasOne(WaterChillerResult::class, 'check_id');
    }
}
