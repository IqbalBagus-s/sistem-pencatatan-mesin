<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Hashidable;

class AutoloaderCheck extends Model
{
    use HasFactory, SoftDeletes, Hashidable;

    protected $fillable = [
        'nomer_autoloader',
        'shift',
        'bulan',
    ];

    /**
     * Get the checker and approver information for this autoloader check.
     */
    public function checkerAndApprover()
    {
        return $this->hasOne(AutoloaderDetail::class, 'tanggal_check_id');
    }

    /**
     * Get the result table 1 entries for this autoloader check.
     */
    public function resultTable1()
    {
        return $this->hasMany(AutoloaderResultTable1::class, 'check_id');
    }

    /**
     * Get the result table 2 entries for this autoloader check.
     */
    public function resultTable2()
    {
        return $this->hasMany(AutoloaderResultTable2::class, 'check_id');
    }

    /**
     * Get the result table 3 entries for this autoloader check.
     */
    public function resultTable3()
    {
        return $this->hasMany(AutoloaderResultTable3::class, 'check_id');
    }
}