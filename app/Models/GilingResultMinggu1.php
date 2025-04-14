<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GilingResultMinggu1 extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'giling_result_minggu1';
    
    protected $fillable = [
        'check_id',
        'checked_items',
        'g1',
        'g2',
        'g3',
        'g4',
        'g5',
        'g6',
        'g7',
        'g8',
        'g9',
        'g10',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the giling check that owns this result.
     */
    public function gilingCheck()
    {
        return $this->belongsTo(GilingCheck::class, 'check_id');
    }
}