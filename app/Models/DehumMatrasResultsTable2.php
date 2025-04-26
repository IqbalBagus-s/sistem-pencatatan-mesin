<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DehumMatrasResultsTable2 extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'dehum_matras_results_table2';
    
    protected $fillable = [
        'check_id',
        'checked_items',
        'tanggal12',
        'tanggal13',
        'tanggal14',
        'tanggal15',
        'tanggal16',
        'tanggal17',
        'tanggal18',
        'tanggal19',
        'tanggal20',
        'tanggal21',
        'tanggal22'
    ];
    
    // Relasi ke DehumMatrasCheck
    public function check()
    {
        return $this->belongsTo(DehumMatrasCheck::class, 'check_id');
    }
}