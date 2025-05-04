<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SlettingCheck extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nomer_sletting',
        'bulan',
        'checked_by_minggu1',
        'approved_by_minggu1',
        'checked_by_minggu2',
        'approved_by_minggu2',
        'checked_by_minggu3',
        'approved_by_minggu3',
        'checked_by_minggu4',
        'approved_by_minggu4',
    ];

    /**
     * Get the results for this sletting check.
     */
    public function results()
    {
        return $this->hasMany(SlettingResult::class, 'check_id');
    }
}