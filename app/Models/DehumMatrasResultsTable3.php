<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DehumMatrasResultsTable3 extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'dehum_matras_results_table3';
    
    protected $fillable = [
        'check_id',
        'checked_items',
        'tanggal23',
        'tanggal24',
        'tanggal25',
        'tanggal26',
        'tanggal27',
        'tanggal28',
        'tanggal29',
        'tanggal30',
        'tanggal31'
    ];
    
    // Relasi ke DehumMatrasCheck
    public function check()
    {
        return $this->belongsTo(DehumMatrasCheck::class, 'check_id');
    }
}