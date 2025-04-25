<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DehumMatrasDetail extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'dehum_matras_details';
    
    protected $fillable = [
        'tanggal_check_id',
        'tanggal',
        'checked_by',
        'approved_by'
    ];
    
    // Relasi ke DehumMatrasCheck
    public function check()
    {
        return $this->belongsTo(DehumMatrasCheck::class, 'tanggal_check_id');
    }
}