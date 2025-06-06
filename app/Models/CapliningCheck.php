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
        'checker_id1',
        'checker_id2',
        'checker_id3',
        'checker_id4',
        'checker_id5',
        'approver_id1',
        'approver_id2',
        'approver_id3',
        'approver_id4',
        'approver_id5',
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
     * Method untuk update status berdasarkan semua approver_id1-5
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
        return !empty($this->approver_id1) && $this->approver_id1 !== null &&
               !empty($this->approver_id2) && $this->approver_id2 !== null &&
               !empty($this->approver_id3) && $this->approver_id3 !== null &&
               !empty($this->approver_id4) && $this->approver_id4 !== null &&
               !empty($this->approver_id5) && $this->approver_id5 !== null;
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
     * Mutator untuk approver_id3 yang otomatis update status
     */
    public function setApproverId3Attribute($value)
    {
        $this->attributes['approver_id3'] = $value;
        $this->updateStatusFromMutator();
    }

    /**
     * Mutator untuk approver_id4 yang otomatis update status
     */
    public function setApproverId4Attribute($value)
    {
        $this->attributes['approver_id4'] = $value;
        $this->updateStatusFromMutator();
    }

    /**
     * Mutator untuk approver_id5 yang otomatis update status
     */
    public function setApproverId5Attribute($value)
    {
        $this->attributes['approver_id5'] = $value;
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
        return !empty($this->attributes['approver_id1']) && $this->attributes['approver_id1'] !== null &&
               !empty($this->attributes['approver_id2']) && $this->attributes['approver_id2'] !== null &&
               !empty($this->attributes['approver_id3']) && $this->attributes['approver_id3'] !== null &&
               !empty($this->attributes['approver_id4']) && $this->attributes['approver_id4'] !== null &&
               !empty($this->attributes['approver_id5']) && $this->attributes['approver_id5'] !== null;
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
    public function approveAll($approverId1, $approverId2, $approverId3, $approverId4, $approverId5)
    {
        $this->approver_id1 = $approverId1;
        $this->approver_id2 = $approverId2;
        $this->approver_id3 = $approverId3;
        $this->approver_id4 = $approverId4;
        $this->approver_id5 = $approverId5;
        $this->status = 'disetujui';
        $this->save();
        
        return $this;
    }

    /**
     * Method helper untuk approval check tertentu
     */
    public function approveCheck($checkNumber, $approverId)
    {
        if ($checkNumber >= 1 && $checkNumber <= 5) {
            $this->{"approver_id{$checkNumber}"} = $approverId;
            $this->save();
        }
        
        return $this;
    }

    /**
     * Method helper untuk assign checker tertentu
     */
    public function assignChecker($checkNumber, $checkerId)
    {
        if ($checkNumber >= 1 && $checkNumber <= 5) {
            $this->{"checker_id{$checkNumber}"} = $checkerId;
            $this->save();
        }
        
        return $this;
    }

    /**
     * Method helper untuk unapprove semua check
     */
    public function unapproveAll()
    {
        $this->approver_id1 = null;
        $this->approver_id2 = null;
        $this->approver_id3 = null;
        $this->approver_id4 = null;
        $this->approver_id5 = null;
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
            $this->{"approver_id{$checkNumber}"} = null;
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
            $fieldName = "approver_id{$checkNumber}";
            return !empty($this->$fieldName) && $this->$fieldName !== null;
        }
        return false;
    }

    /**
     * Check apakah check tertentu sudah memiliki checker
     */
    public function hasChecker($checkNumber)
    {
        if ($checkNumber >= 1 && $checkNumber <= 5) {
            $fieldName = "checker_id{$checkNumber}";
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

    // ========== RELASI DENGAN MODEL CHECKER ==========
    
    /**
     * Relasi dengan Checker untuk check 1
     */
    public function checker1()
    {
        return $this->belongsTo(Checker::class, 'checker_id1');
    }

    /**
     * Relasi dengan Checker untuk check 2
     */
    public function checker2()
    {
        return $this->belongsTo(Checker::class, 'checker_id2');
    }

    /**
     * Relasi dengan Checker untuk check 3
     */
    public function checker3()
    {
        return $this->belongsTo(Checker::class, 'checker_id3');
    }

    /**
     * Relasi dengan Checker untuk check 4
     */
    public function checker4()
    {
        return $this->belongsTo(Checker::class, 'checker_id4');
    }

    /**
     * Relasi dengan Checker untuk check 5
     */
    public function checker5()
    {
        return $this->belongsTo(Checker::class, 'checker_id5');
    }

    // ========== RELASI DENGAN MODEL APPROVER ==========
    
    /**
     * Relasi dengan Approver untuk check 1
     */
    public function approver1()
    {
        return $this->belongsTo(Approver::class, 'approver_id1');
    }

    /**
     * Relasi dengan Approver untuk check 2
     */
    public function approver2()
    {
        return $this->belongsTo(Approver::class, 'approver_id2');
    }

    /**
     * Relasi dengan Approver untuk check 3
     */
    public function approver3()
    {
        return $this->belongsTo(Approver::class, 'approver_id3');
    }

    /**
     * Relasi dengan Approver untuk check 4
     */
    public function approver4()
    {
        return $this->belongsTo(Approver::class, 'approver_id4');
    }

    /**
     * Relasi dengan Approver untuk check 5
     */
    public function approver5()
    {
        return $this->belongsTo(Approver::class, 'approver_id5');
    }

    // ========== HELPER METHODS UNTUK RELASI ==========

    /**
     * Get checker untuk check tertentu
     */
    public function getChecker($checkNumber)
    {
        if ($checkNumber >= 1 && $checkNumber <= 5) {
            return $this->{"checker{$checkNumber}"};
        }
        return null;
    }

    /**
     * Get approver untuk check tertentu
     */
    public function getApprover($checkNumber)
    {
        if ($checkNumber >= 1 && $checkNumber <= 5) {
            return $this->{"approver{$checkNumber}"};
        }
        return null;
    }

    /**
     * Get semua checker yang terkait
     */
    public function getAllCheckers()
    {
        $checkers = [];
        for ($i = 1; $i <= 5; $i++) {
            $checker = $this->getChecker($i);
            if ($checker) {
                $checkers["check{$i}"] = $checker;
            }
        }
        return $checkers;
    }

    /**
     * Get semua approver yang terkait
     */
    public function getAllApprovers()
    {
        $approvers = [];
        for ($i = 1; $i <= 5; $i++) {
            $approver = $this->getApprover($i);
            if ($approver) {
                $approvers["check{$i}"] = $approver;
            }
        }
        return $approvers;
    }

    /**
     * Relasi dengan tabel caplining_results (one-to-many).
     */
    public function results()
    {
        return $this->hasMany(CapliningResult::class, 'check_id');
    }
}