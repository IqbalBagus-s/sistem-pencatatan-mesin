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
    public function resultMinggu1()
    {
        return $this->hasOne(GilingResultMinggu1::class, 'check_id');
    }

    /**
     * Get the result for minggu 2.
     */
    public function resultMinggu2()
    {
        return $this->hasOne(GilingResultMinggu2::class, 'check_id');
    }

    /**
     * Get the result for minggu 3.
     */
    public function resultMinggu3()
    {
        return $this->hasOne(GilingResultMinggu3::class, 'check_id');
    }

    /**
     * Get the result for minggu 4.
     */
    public function resultMinggu4()
    {
        return $this->hasOne(GilingResultMinggu4::class, 'check_id');
    }
    
    /**
     * Get all results for this check regardless of week.
     */
    public function allResults()
    {
        return [
            $this->resultMinggu1,
            $this->resultMinggu2,
            $this->resultMinggu3,
            $this->resultMinggu4,
        ];
    }
}