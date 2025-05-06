<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CraneMatrasResult extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'crane_matras_results';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'check_id',
        'checked_items',
        'check',
        'keterangan',
    ];

    /**
     * Get the check that owns the result.
     */
    public function check()
    {
        return $this->belongsTo(CraneMatrasCheck::class, 'check_id');
    }
}