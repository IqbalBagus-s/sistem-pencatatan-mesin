<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HopperResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hopper_results';

    protected $fillable = [
        'check_id',
        'checked_items',
        'minggu1',
        'keterangan_minggu1',
        'minggu2',
        'keterangan_minggu2',
        'minggu3',
        'keterangan_minggu3',
        'minggu4',
        'keterangan_minggu4',
    ];

    /**
     * Relasi dengan tabel hopper_checks (many-to-one).
     */
    public function check()
    {
        return $this->belongsTo(HopperCheck::class, 'check_id');
    }
}
