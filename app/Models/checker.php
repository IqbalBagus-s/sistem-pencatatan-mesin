<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Checker extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $table = 'checkers'; // Nama tabel

    protected $fillable = [
        'username',
        'password',
        'status', 
    ];

    protected $hidden = [
        'password',
    ];

    protected $dates = ['deleted_at']; // Untuk soft delete

    protected $attributes = [
        'status' => 'aktif', // Default status adalah 'aktif'
    ];
}
