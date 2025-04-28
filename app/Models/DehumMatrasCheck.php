<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DehumMatrasCheck extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'dehum_matras_checks';
    
    protected $fillable = [
        'nomer_dehum_matras',
        'shift',
        'bulan'
    ];
    
    // Relasi one-to-many ke DehumMatrasDetail
    public function detail()
    {
        return $this->hasOne(DehumMatrasDetail::class, 'tanggal_check_id');
    }
    
    // Relasi one-to-many ke tabel hasil
    public function resultTable1()
    {
        return $this->hasMany(DehumMatrasResultsTable1::class, 'check_id');
    }
    
    public function resultTable2()
    {
        return $this->hasMany(DehumMatrasResultsTable2::class, 'check_id');
    }
    
    public function resultTable3()
    {
        return $this->hasMany(DehumMatrasResultsTable3::class, 'check_id');
    }
}