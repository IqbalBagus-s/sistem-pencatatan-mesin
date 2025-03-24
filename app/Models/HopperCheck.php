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
        'tanggal',
        'checked_by',
        'approved_by',
    ];

    /**
     * Relasi dengan tabel hopper_results (one-to-many).
     */
    public function results()
    {
        return $this->hasMany(HopperResult::class, 'check_id');
    }
}
