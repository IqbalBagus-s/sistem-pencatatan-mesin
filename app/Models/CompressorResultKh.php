<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompressorResultKh extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'compressor_kh_results'; // Nama tabel di database

    protected $fillable = [
        'check_id',
        'checked_items',
        'kh_7I', 'kh_7II', 'kh_8I', 'kh_8II',
        'kh_9I', 'kh_9II', 'kh_10I', 'kh_10II',
        'kh_11I', 'kh_11II',
    ];

    /**
     * Relasi ke CompressorCheck (Many to One)
     */
    public function check()
    {
        return $this->belongsTo(CompressorCheck::class, 'check_id');
    }
}
