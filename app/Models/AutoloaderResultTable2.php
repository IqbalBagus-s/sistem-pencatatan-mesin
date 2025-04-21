<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AutoloaderResultTable2 extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'autoloader_result_table2';

    protected $fillable = [
        'check_id',
        'checked_items',
        'tanggal12', 'keterangan_tanggal12',
        'tanggal13', 'keterangan_tanggal13',
        'tanggal14', 'keterangan_tanggal14',
        'tanggal15', 'keterangan_tanggal15',
        'tanggal16', 'keterangan_tanggal16',
        'tanggal17', 'keterangan_tanggal17',
        'tanggal18', 'keterangan_tanggal18',
        'tanggal19', 'keterangan_tanggal19',
        'tanggal20', 'keterangan_tanggal20',
        'tanggal21', 'keterangan_tanggal21',
        'tanggal22', 'keterangan_tanggal22',
    ];

    /**
     * Get the autoloader check that owns this result table entry.
     */
    public function autoloaderCheck()
    {
        return $this->belongsTo(AutoloaderCheck::class, 'check_id');
    }
}