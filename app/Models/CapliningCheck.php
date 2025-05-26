<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CapliningCheck extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'caplining_checks';

    protected $fillable = [
        'nomer_caplining',
        'tanggal_check1',
        'tanggal_check2',
        'tanggal_check3',
        'tanggal_check4',
        'tanggal_check5',
        'checked_by1',
        'checked_by2',
        'checked_by3',
        'checked_by4',
        'checked_by5',
        'approved_by1',
        'approved_by2',
        'approved_by3',
        'approved_by4',
        'approved_by5',
        'status'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'tanggal_check1',
        'tanggal_check2',
        'tanggal_check3',
        'tanggal_check4',
        'tanggal_check5'
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
     * Method untuk update status berdasarkan semua approved_by1-5
     * Status disetujui hanya jika SEMUA check sudah disetujui
     */
    private function updateStatus()
    {
        if ($this->isAllChecksApproved()) {
            $this->status = 'disetujui';
        } else {
            $this->status = 'belum_disetujui';
        }
    }

    /**
     * Helper method untuk mengecek apakah semua check sudah disetujui
     */
    private function isAllChecksApproved()
    {
        return !empty($this->approved_by1) && $this->approved_by1 !== null &&
               !empty($this->approved_by2) && $this->approved_by2 !== null &&
               !empty($this->approved_by3) && $this->approved_by3 !== null &&
               !empty($this->approved_by4) && $this->approved_by4 !== null &&
               !empty($this->approved_by5) && $this->approved_by5 !== null;
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
     * Mutator untuk approved_by3 yang otomatis update status
     */
    public function setApprovedBy3Attribute($value)
    {
        $this->attributes['approved_by3'] = $value;
        $this->updateStatusFromMutator();
    }

    /**
     * Mutator untuk approved_by4 yang otomatis update status
     */
    public function setApprovedBy4Attribute($value)
    {
        $this->attributes['approved_by4'] = $value;
        $this->updateStatusFromMutator();
    }

    /**
     * Mutator untuk approved_by5 yang otomatis update status
     */
    public function setApprovedBy5Attribute($value)
    {
        $this->attributes['approved_by5'] = $value;
        $this->updateStatusFromMutator();
    }

    /**
     * Helper method untuk update status dari mutator
     */
    private function updateStatusFromMutator()
    {
        if ($this->isAllChecksApprovedFromAttributes()) {
            $this->attributes['status'] = 'disetujui';
        } else {
            $this->attributes['status'] = 'belum_disetujui';
        }
    }

    /**
     * Helper method untuk mengecek approval dari attributes (untuk mutator)
     */
    private function isAllChecksApprovedFromAttributes()
    {
        return !empty($this->attributes['approved_by1']) && $this->attributes['approved_by1'] !== null &&
               !empty($this->attributes['approved_by2']) && $this->attributes['approved_by2'] !== null &&
               !empty($this->attributes['approved_by3']) && $this->attributes['approved_by3'] !== null &&
               !empty($this->attributes['approved_by4']) && $this->attributes['approved_by4'] !== null &&
               !empty($this->attributes['approved_by5']) && $this->attributes['approved_by5'] !== null;
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
     * Method helper untuk approval lengkap (semua check)
     */
    public function approveAll($approvedBy1, $approvedBy2, $approvedBy3, $approvedBy4, $approvedBy5)
    {
        $this->approved_by1 = $approvedBy1;
        $this->approved_by2 = $approvedBy2;
        $this->approved_by3 = $approvedBy3;
        $this->approved_by4 = $approvedBy4;
        $this->approved_by5 = $approvedBy5;
        $this->status = 'disetujui';
        $this->save();
        
        return $this;
    }

    /**
     * Method helper untuk approval check tertentu
     */
    public function approveCheck($checkNumber, $approvedBy)
    {
        if ($checkNumber >= 1 && $checkNumber <= 5) {
            $this->{"approved_by{$checkNumber}"} = $approvedBy;
            $this->save();
        }
        
        return $this;
    }

    /**
     * Method helper untuk unapprove semua check
     */
    public function unapproveAll()
    {
        $this->approved_by1 = null;
        $this->approved_by2 = null;
        $this->approved_by3 = null;
        $this->approved_by4 = null;
        $this->approved_by5 = null;
        $this->status = 'belum_disetujui';
        $this->save();
        
        return $this;
    }

    /**
     * Method helper untuk unapprove check tertentu
     */
    public function unapproveCheck($checkNumber)
    {
        if ($checkNumber >= 1 && $checkNumber <= 5) {
            $this->{"approved_by{$checkNumber}"} = null;
            $this->save();
        }
        
        return $this;
    }

    /**
     * Check apakah sudah disetujui lengkap (semua check)
     */
    public function isApproved()
    {
        return $this->status === 'disetujui';
    }

    /**
     * Check apakah check tertentu sudah disetujui
     */
    public function isCheckApproved($checkNumber)
    {
        if ($checkNumber >= 1 && $checkNumber <= 5) {
            $fieldName = "approved_by{$checkNumber}";
            return !empty($this->$fieldName) && $this->$fieldName !== null;
        }
        return false;
    }

    /**
     * Get jumlah check yang sudah disetujui
     */
    public function getApprovedChecksCount()
    {
        $count = 0;
        for ($i = 1; $i <= 5; $i++) {
            if ($this->isCheckApproved($i)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Get persentase approval
     */
    public function getApprovalPercentage()
    {
        return ($this->getApprovedChecksCount() / 5) * 100;
    }

    /**
     * Get list check yang belum disetujui
     */
    public function getPendingChecks()
    {
        $pending = [];
        for ($i = 1; $i <= 5; $i++) {
            if (!$this->isCheckApproved($i)) {
                $pending[] = $i;
            }
        }
        return $pending;
    }

    /**
     * Relasi dengan tabel caplining_results (one-to-many).
     */
    public function results()
    {
        return $this->hasMany(CapliningResult::class, 'check_id');
    }
}