<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DehumBahanResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dehum_bahan_results';

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
     * Relasi dengan tabel dehum_bahan_results (many-to-one).
     */
    public function check()
    {
        return $this->belongsTo(DehumBahanCheck::class, 'check_id');
    }
}
