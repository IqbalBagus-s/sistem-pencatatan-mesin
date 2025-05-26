<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompressorCheck extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'compressor_checks'; // Nama tabel di database

    protected $fillable = [
        'tanggal',
        'hari',
        'checked_by_shift1',
        'checked_by_shift2',
        'approved_by_shift1',
        'approved_by_shift2',
        'kompressor_on_kl',
        'kompressor_on_kh',
        'mesin_on',
        'mesin_off',
        'temperatur_shift1',
        'temperatur_shift2',
        'humidity_shift1',
        'humidity_shift2',
        'status'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'tanggal'
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
     * Method untuk update status berdasarkan approved_by_shift1 dan approved_by_shift2
     */
    private function updateStatus()
    {
        if (!empty($this->approved_by_shift1) && $this->approved_by_shift1 !== null && 
            !empty($this->approved_by_shift2) && $this->approved_by_shift2 !== null) {
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
     * Mutator untuk approved_by_shift1 yang otomatis update status
     */
    public function setApprovedByShift1Attribute($value)
    {
        $this->attributes['approved_by_shift1'] = $value;
        $this->updateStatusFromMutator();
    }

    /**
     * Mutator untuk approved_by_shift2 yang otomatis update status
     */
    public function setApprovedByShift2Attribute($value)
    {
        $this->attributes['approved_by_shift2'] = $value;
        $this->updateStatusFromMutator();
    }

    /**
     * Helper method untuk update status dari mutator
     */
    private function updateStatusFromMutator()
    {
        if (!empty($this->attributes['approved_by_shift1']) && $this->attributes['approved_by_shift1'] !== null && 
            !empty($this->attributes['approved_by_shift2']) && $this->attributes['approved_by_shift2'] !== null) {
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
     * Method helper untuk approval lengkap (kedua shift)
     */
    public function approve($approvedByShift1, $approvedByShift2)
    {
        $this->approved_by_shift1 = $approvedByShift1;
        $this->approved_by_shift2 = $approvedByShift2;
        $this->status = 'disetujui';
        $this->save();
        
        return $this;
    }

    /**
     * Method helper untuk approval shift pertama
     */
    public function approveShift1($approvedByShift1)
    {
        $this->approved_by_shift1 = $approvedByShift1;
        $this->save();
        
        return $this;
    }

    /**
     * Method helper untuk approval shift kedua
     */
    public function approveShift2($approvedByShift2)
    {
        $this->approved_by_shift2 = $approvedByShift2;
        $this->save();
        
        return $this;
    }

    /**
     * Method helper untuk unapprove
     */
    public function unapprove()
    {
        $this->approved_by_shift1 = null;
        $this->approved_by_shift2 = null;
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
     * Check apakah sudah disetujui shift pertama
     */
    public function isShift1Approved()
    {
        return !empty($this->approved_by_shift1) && $this->approved_by_shift1 !== null;
    }

    /**
     * Check apakah sudah disetujui shift kedua
     */
    public function isShift2Approved()
    {
        return !empty($this->approved_by_shift2) && $this->approved_by_shift2 !== null;
    }

    /**
     * Relasi ke CompressorResult (One to Many)
     */
    public function resultsKh()
    {
        return $this->hasMany(CompressorResultKh::class, 'check_id');
    }

    public function resultsKl()
    {
        return $this->hasMany(CompressorResultKl::class, 'check_id');
    }
}