<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_type',     // 'checker', 'approver', 'host'
        'user_id',       // ID pengguna
        'user_name',     // Nama pengguna untuk referensi
        'action',        // 'created', 'updated', 'deleted', 'approved', 'rejected'
        'description',   // Deskripsi aktivitas
        'target_type',   // 'form', 'approver', 'checker'
        'target_id',     // ID target
        'details',       // JSON untuk detail tambahan
    ];

    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Helper method untuk membuat log aktivitas
    public static function logActivity($userType, $userId, $userName, $action, $description, $targetType = null, $targetId = null, $details = null)
    {
        return self::create([
            'user_type' => $userType,
            'user_id' => $userId,
            'user_name' => $userName,
            'action' => $action,
            'description' => $description,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'details' => $details,
        ]);
    }

    // Scope untuk mendapatkan aktivitas terbaru
    public function scopeRecent($query, $limit = 5)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    // Scope untuk filter berdasarkan tipe user
    public function scopeByUserType($query, $userType)
    {
        return $query->where('user_type', $userType);
    }
}