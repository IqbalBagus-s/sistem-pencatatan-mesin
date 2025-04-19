<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AutoloaderDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'autoloader_details';

    protected $fillable = [
        'tanggal_check_id',
        'tanggal',  // Note: there's a typo in the migration 'tangal' instead of 'tanggal'
        'checked_by',
        'approved_by',
    ];

    /**
     * Get the autoloader check that owns this checker and approver.
     */
    public function autoloaderCheck()
    {
        return $this->belongsTo(AutoloaderCheck::class, 'tanggal_check_id');
    }
}
