<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Hashidable;

class WaterChillerCheck extends Model
{
    use HasFactory, SoftDeletes, Hashidable;

    protected $table = 'water_chiller_checks'; // Nama tabel

    protected $fillable = [
        'tanggal',
        'hari',
        'checker_id',
        'approver_id',
        'keterangan',
        'status'
    ];

    protected $dates = [
        'tanggal',
        'deleted_at'
    ];

    // Event listeners untuk mengatur status otomatis
    protected static function boot()
    {
        parent::boot();

        // Saat data akan disimpan (creating/updating)
        static::saving(function ($model) {
            $model->updateStatus();
        });
    }

    /**
     * Method untuk update status berdasarkan approver_id
     */
    private function updateStatus()
    {
        if (!empty($this->approver_id) && $this->approver_id !== null) {
            $this->status = 'disetujui';
        } else {
            $this->status = 'belum_disetujui';
        }
    }

    /**
     * Accessor untuk mendapatkan status dalam format yang mudah dibaca
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'disetujui' => 'Disetujui',
            'belum_disetujui' => 'Belum Disetujui',
            default => 'Status Tidak Dikenal'
        };
    }

    /**
     * Mutator untuk approver_id yang otomatis update status
     */
    public function setApproverIdAttribute($value)
    {
        $this->attributes['approver_id'] = $value;
        // Update status berdasarkan approver_id
        if (!empty($value) && $value !== null) {
            $this->attributes['status'] = 'disetujui';
        } else {
            $this->attributes['status'] = 'belum_disetujui';
        }
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeDisetujui($query)
    {
        return $query->where('status', 'disetujui');
    }

    public function scopeBelumDisetujui($query)
    {
        return $query->where('status', 'belum_disetujui');
    }

    /**
     * Method helper untuk approval
     */
    public function approve($approverId)
    {
        $this->approver_id = $approverId;
        $this->status = 'disetujui';
        $this->save();
        
        return $this;
    }

    /**
     * Method helper untuk unapprove
     */
    public function unapprove()
    {
        $this->approver_id = null;
        $this->status = 'belum_disetujui';
        $this->save();
        
        return $this;
    }

    /**
     * Check apakah sudah disetujui
     */
    public function isApproved()
    {
        return $this->status === 'disetujui';
    }

    // Relasi antar table
    public function results()
    {
        return $this->hasOne(WaterChillerResult::class, 'check_id');
    }

    // Relasi ke Checker
    public function checker()
    {
        return $this->belongsTo(Checker::class, 'checker_id');
    }

    // Relasi ke Approver
    public function approver()
    {
        return $this->belongsTo(Approver::class, 'approver_id');
    }
}