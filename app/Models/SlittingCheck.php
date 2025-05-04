<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SlittingCheck extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nomer_slitting',
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
     * Get the results for this slitting check.
     */
    public function results()
    {
        return $this->hasMany(SlittingResult::class, 'check_id');
    }
}