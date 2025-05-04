<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SlettingResult extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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
     * Get the sletting check that owns this result.
     */
    public function slettingCheck()
    {
        return $this->belongsTo(SlettingCheck::class, 'check_id');
    }
}