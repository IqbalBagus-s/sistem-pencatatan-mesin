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
        'checker_id',
        'approver_id1',
        'approval_date1',
        'approver_id2',
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
     * Method untuk update status berdasarkan approver_id1 dan approver_id2
     */
    private function updateStatus()
    {
        if (!empty($this->approver_id1) && $this->approver_id1 !== null && 
            !empty($this->approver_id2) && $this->approver_id2 !== null) {
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
     * Mutator untuk approver_id1 yang otomatis update status
     */
    public function setApproverId1Attribute($value)
    {
        $this->attributes['approver_id1'] = $value;
        $this->updateStatusFromMutator();
    }

    /**
     * Mutator untuk approver_id2 yang otomatis update status
     */
    public function setApproverId2Attribute($value)
    {
        $this->attributes['approver_id2'] = $value;
        $this->updateStatusFromMutator();
    }

    /**
     * Helper method untuk update status dari mutator
     */
    private function updateStatusFromMutator()
    {
        if (!empty($this->attributes['approver_id1']) && $this->attributes['approver_id1'] !== null && 
            !empty($this->attributes['approver_id2']) && $this->attributes['approver_id2'] !== null) {
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
    public function approve($approverId1, $approverId2, $approvalDate1 = null)
    {
        $this->approver_id1 = $approverId1;
        $this->approver_id2 = $approverId2;
        $this->approval_date1 = $approvalDate1 ?: now();
        $this->status = 'disetujui';
        $this->save();
        
        return $this;
    }

    /**
     * Method helper untuk approval pertama
     */
    public function approveFirst($approverId1, $approvalDate1 = null)
    {
        $this->approver_id1 = $approverId1;
        $this->approval_date1 = $approvalDate1 ?: now();
        $this->save();
        
        return $this;
    }

    /**
     * Method helper untuk approval kedua
     */
    public function approveSecond($approverId2)
    {
        $this->approver_id2 = $approverId2;
        $this->save();
        
        return $this;
    }

    /**
     * Method helper untuk unapprove
     */
    public function unapprove()
    {
        $this->approver_id1 = null;
        $this->approver_id2 = null;
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
        return !empty($this->approver_id1) && $this->approver_id1 !== null;
    }

    /**
     * Check apakah sudah disetujui kedua
     */
    public function isSecondApproved()
    {
        return !empty($this->approver_id2) && $this->approver_id2 !== null;
    }

    /**
     * Get the result for giling check
     */
    public function result()
    {
        return $this->hasMany(GilingResult::class, 'check_id');
    }

    // Relasi ke Checker
    public function checker()
    {
        return $this->belongsTo(Checker::class, 'checker_id');
    }

    // Relasi ke Approver 1
    public function approver1()
    {
        return $this->belongsTo(Approver::class, 'approver_id1');
    }

    // Relasi ke Approver 2
    public function approver2()
    {
        return $this->belongsTo(Approver::class, 'approver_id2');
    }
}