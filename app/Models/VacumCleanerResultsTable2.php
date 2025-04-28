<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VacumCleanerResultsTable2 extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vacum_cleaner_results_table2';

    protected $fillable = [
        'check_id',
        'checked_items',
        'minggu4',
        'keterangan_minggu4',
    ];

    /**
     * Mendapatkan data pemeriksaan yang terkait
     */
    public function pemeriksaan()
    {
        return $this->belongsTo(VacumCleanerCheck::class, 'check_id');
    }
}