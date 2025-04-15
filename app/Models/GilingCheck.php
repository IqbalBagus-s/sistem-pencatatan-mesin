<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GilingCheck extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'giling_checks';
    
    protected $fillable = [
        'bulan',
        'minggu',
        'checked_by',
        'approved_by1',
        'approved_by2',
        'keterangan',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the result for minggu 1.
     */
    public function result()
    {
        return $this->hasMany(GilingResult::class, 'check_id');
    }
}