<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompressorResult extends Model
{
    use HasFactory;

    protected $table = 'compressor_results'; // Nama tabel di database

    protected $fillable = [
        'check_id',
        'checked_items',
        'kl_10I', 'kl_10II', 'kl_5I', 'kl_5II',
        'kl_6I', 'kl_6II', 'kl_7I', 'kl_7II',
        'kl_8I', 'kl_8II', 'kl_9I', 'kl_9II',
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
