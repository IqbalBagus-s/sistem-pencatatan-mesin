<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CraneMatrasCheck extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'crane_matras_checks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nomer_crane_matras',
        'bulan',
        'tanggal',
        'checked_by',
        'approved_by',
    ];

    /**
     * Get the results associated with the check.
     */
    public function results()
    {
        return $this->hasMany(CraneMatrasResult::class, 'check_id');
    }
}