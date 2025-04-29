<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DehumMatrasResultsTable1 extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'dehum_matras_results_table1';
    
    protected $fillable = [
        'check_id',
        'checked_items',
        'tanggal1',
        'tanggal2',
        'tanggal3',
        'tanggal4',
        'tanggal5',
        'tanggal6',
        'tanggal7',
        'tanggal8',
        'tanggal9',
        'tanggal10',
        'tanggal11'
    ];
    
    // Relasi ke DehumMatrasCheck
    public function check()
    {
        return $this->belongsTo(DehumMatrasCheck::class, 'check_id');
    }
}