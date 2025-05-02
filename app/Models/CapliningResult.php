<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CapliningResult extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'caplining_results';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'check_id',
        'checked_items',
        'check1',
        'check2',
        'check3',
        'check4',
        'check5',
        'keterangan1',
        'keterangan2',
        'keterangan3',
        'keterangan4',
        'keterangan5',
    ];

    /**
     * Get the cap lining check that owns the result.
     */
    public function capLiningCheck()
    {
        return $this->belongsTo(CapliningCheck::class, 'check_id');
    }
}