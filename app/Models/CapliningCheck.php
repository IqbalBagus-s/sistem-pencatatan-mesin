<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CapliningCheck extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'caplining_checks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nomer_caplining',
        'tanggal_check1',
        'tanggal_check2',
        'tanggal_check3',
        'tanggal_check4',
        'tanggal_check5',
        'checked_by1',
        'checked_by2',
        'checked_by3',
        'checked_by4',
        'checked_by5',
        'approved_by1',
        'approved_by2',
        'approved_by3',
        'approved_by4',
        'approved_by5',
    ];

    /**
     * Get the results for the cap lining check.
     */
    public function results()
    {
        return $this->hasMany(CapliningResult::class, 'check_id');
    }
}