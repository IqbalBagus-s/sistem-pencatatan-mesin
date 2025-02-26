<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Approver extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $table = 'approvers'; // Nama tabel

    protected $fillable = [
        'username',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    protected $dates = ['deleted_at']; // Untuk soft delete
}
