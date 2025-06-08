<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Hashidable;

class CompressorCheck extends Model
{
    use HasFactory, SoftDeletes, Hashidable;

    protected $table = 'compressor_checks'; // Nama tabel di database

    protected $fillable = [
        'tanggal',
        'hari',
        'checker_shift1_id',
        'checker_shift2_id',
        'approver_shift1_id',
        'approver_shift2_id',
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
     * Method untuk update status berdasarkan approver_shift1_id dan approver_shift2_id
     */
    private function updateStatus()
    {
        if (!empty($this->approver_shift1_id) && $this->approver_shift1_id !== null && 
            !empty($this->approver_shift2_id) && $this->approver_shift2_id !== null) {
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
     * Mutator untuk approver_shift1_id yang otomatis update status
     */
    public function setApproverShift1IdAttribute($value)
    {
        $this->attributes['approver_shift1_id'] = $value;
        $this->updateStatusFromMutator();
    }

    /**
     * Mutator untuk approver_shift2_id yang otomatis update status
     */
    public function setApproverShift2IdAttribute($value)
    {
        $this->attributes['approver_shift2_id'] = $value;
        $this->updateStatusFromMutator();
    }

    /**
     * Helper method untuk update status dari mutator
     */
    private function updateStatusFromMutator()
    {
        if (!empty($this->attributes['approver_shift1_id']) && $this->attributes['approver_shift1_id'] !== null && 
            !empty($this->attributes['approver_shift2_id']) && $this->attributes['approver_shift2_id'] !== null) {
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
    public function approve($approverShift1Id, $approverShift2Id)
    {
        $this->approver_shift1_id = $approverShift1Id;
        $this->approver_shift2_id = $approverShift2Id;
        $this->status = 'disetujui';
        $this->save();
        
        return $this;
    }

    /**
     * Method helper untuk approval shift pertama
     */
    public function approveShift1($approverShift1Id)
    {
        $this->approver_shift1_id = $approverShift1Id;
        $this->save();
        
        return $this;
    }

    /**
     * Method helper untuk approval shift kedua
     */
    public function approveShift2($approverShift2Id)
    {
        $this->approver_shift2_id = $approverShift2Id;
        $this->save();
        
        return $this;
    }

    /**
     * Method helper untuk unapprove
     */
    public function unapprove()
    {
        $this->approver_shift1_id = null;
        $this->approver_shift2_id = null;
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
        return !empty($this->approver_shift1_id) && $this->approver_shift1_id !== null;
    }

    /**
     * Check apakah sudah disetujui shift kedua
     */
    public function isShift2Approved()
    {
        return !empty($this->approver_shift2_id) && $this->approver_shift2_id !== null;
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

    // Relasi ke Checker Shift 1
    public function checkerShift1()
    {
        return $this->belongsTo(Checker::class, 'checker_shift1_id');
    }

    // Relasi ke Checker Shift 2
    public function checkerShift2()
    {
        return $this->belongsTo(Checker::class, 'checker_shift2_id');
    }

    // Relasi ke Approver Shift 1
    public function approverShift1()
    {
        return $this->belongsTo(Approver::class, 'approver_shift1_id');
    }

    // Relasi ke Approver Shift 2
    public function approverShift2()
    {
        return $this->belongsTo(Approver::class, 'approver_shift2_id');
    }
}