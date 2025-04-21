<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AutoloaderResultTable1 extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'autoloader_result_table1';

    protected $fillable = [
        'check_id',
        'checked_items',
        'tanggal1', 'keterangan_tanggal1',
        'tanggal2', 'keterangan_tanggal2',
        'tanggal3', 'keterangan_tanggal3',
        'tanggal4', 'keterangan_tanggal4',
        'tanggal5', 'keterangan_tanggal5',
        'tanggal6', 'keterangan_tanggal6',
        'tanggal7', 'keterangan_tanggal7',
        'tanggal8', 'keterangan_tanggal8',
        'tanggal9', 'keterangan_tanggal9',
        'tanggal10', 'keterangan_tanggal10',
        'tanggal11', 'keterangan_tanggal11',
    ];

    /**
     * Get the autoloader check that owns this result table entry.
     */
    public function autoloaderCheck()
    {
        return $this->belongsTo(AutoloaderCheck::class, 'check_id');
    }
}