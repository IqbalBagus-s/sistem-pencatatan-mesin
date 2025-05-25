<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GilingCheck extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'giling_checks';
    
    protected $fillable = [
        'bulan',
        'minggu',
        'checked_by',
        'approved_by1',
        'approval_date1',
        'approved_by2',
        'keterangan',
        'status'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'approval_date1'
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
     * Method untuk update status berdasarkan approved_by1 dan approved_by2
     */
    private function updateStatus()
    {
        if (!empty($this->approved_by1) && $this->approved_by1 !== null && 
            !empty($this->approved_by2) && $this->approved_by2 !== null) {
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
     * Mutator untuk approved_by1 yang otomatis update status
     */
    public function setApprovedBy1Attribute($value)
    {
        $this->attributes['approved_by1'] = $value;
        $this->updateStatusFromMutator();
    }

    /**
     * Mutator untuk approved_by2 yang otomatis update status
     */
    public function setApprovedBy2Attribute($value)
    {
        $this->attributes['approved_by2'] = $value;
        $this->updateStatusFromMutator();
    }

    /**
     * Helper method untuk update status dari mutator
     */
    private function updateStatusFromMutator()
    {
        if (!empty($this->attributes['approved_by1']) && $this->attributes['approved_by1'] !== null && 
            !empty($this->attributes['approved_by2']) && $this->attributes['approved_by2'] !== null) {
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
     * Method helper untuk approval lengkap (kedua approver)
     */
    public function approve($approvedBy1, $approvedBy2, $approvalDate1 = null)
    {
        $this->approved_by1 = $approvedBy1;
        $this->approved_by2 = $approvedBy2;
        $this->approval_date1 = $approvalDate1 ?: now();
        $this->status = 'disetujui';
        $this->save();
        
        return $this;
    }

    /**
     * Method helper untuk approval pertama
     */
    public function approveFirst($approvedBy1, $approvalDate1 = null)
    {
        $this->approved_by1 = $approvedBy1;
        $this->approval_date1 = $approvalDate1 ?: now();
        $this->save();
        
        return $this;
    }

    /**
     * Method helper untuk approval kedua
     */
    public function approveSecond($approvedBy2)
    {
        $this->approved_by2 = $approvedBy2;
        $this->save();
        
        return $this;
    }

    /**
     * Method helper untuk unapprove
     */
    public function unapprove()
    {
        $this->approved_by1 = null;
        $this->approved_by2 = null;
        $this->approval_date1 = null;
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

    /**
     * Check apakah sudah disetujui pertama
     */
    public function isFirstApproved()
    {
        return !empty($this->approved_by1) && $this->approved_by1 !== null;
    }

    /**
     * Check apakah sudah disetujui kedua
     */
    public function isSecondApproved()
    {
        return !empty($this->approved_by2) && $this->approved_by2 !== null;
    }

    /**
     * Get the result for giling check
     */
    public function result()
    {
        return $this->hasMany(GilingResult::class, 'check_id');
    }
}