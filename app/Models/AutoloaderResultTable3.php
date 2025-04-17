<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AutoloaderResultTable3 extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'autoloader_result_table3';

    protected $fillable = [
        'check_id',
        'checked_items',
        'tanggal23', 'keterangan_tanggal23',
        'tanggal24', 'keterangan_tanggal24',
        'tanggal25', 'keterangan_tanggal25',
        'tanggal26', 'keterangan_tanggal26',
        'tanggal27', 'keterangan_tanggal27',
        'tanggal28', 'keterangan_tanggal28',
        'tanggal29', 'keterangan_tanggal29',
        'tanggal30', 'keterangan_tanggal30',
        'tanggal31', 'keterangan_tanggal31',
    ];

    /**
     * Get the autoloader check that owns this result table entry.
     */
    public function autoloaderCheck()
    {
        return $this->belongsTo(AutoloaderCheck::class, 'check_id');
    }
}