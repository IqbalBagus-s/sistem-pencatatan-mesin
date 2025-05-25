<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AirDryerCheck extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tanggal',
        'hari',
        'checked_by',
        'approved_by',
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
     * Method untuk update status berdasarkan approved_by
     */
    private function updateStatus()
    {
        if (!empty($this->approved_by) && $this->approved_by !== null) {
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
     * Mutator untuk approved_by yang otomatis update status
     */
    public function setApprovedByAttribute($value)
    {
        $this->attributes['approved_by'] = $value;
        
        // Update status berdasarkan approved_by
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
    public function approve($approvedBy)
    {
        $this->approved_by = $approvedBy;
        $this->status = 'disetujui';
        $this->save();
        
        return $this;
    }

    /**
     * Method helper untuk unapprove
     */
    public function unapprove()
    {
        $this->approved_by = null;
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
}