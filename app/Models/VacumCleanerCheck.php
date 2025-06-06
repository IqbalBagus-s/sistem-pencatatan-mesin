<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VacumCleanerCheck extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vacum_cleaner_checks';

    protected $fillable = [
        'nomer_vacum_cleaner',
        'bulan',
        'tanggal_dibuat_minggu2',
        'tanggal_dibuat_minggu4',
        'checker_minggu2_id',
        'checker_minggu4_id',
        'approver_minggu2_id',
        'approver_minggu4_id',
        'status'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'tanggal_dibuat_minggu2',
        'tanggal_dibuat_minggu4'
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
     * Relasi dengan model Checker untuk minggu 2
     */
    public function checkerMinggu2()
    {
        return $this->belongsTo(Checker::class, 'checker_minggu2_id');
    }

    /**
     * Relasi dengan model Checker untuk minggu 4
     */
    public function checkerMinggu4()
    {
        return $this->belongsTo(Checker::class, 'checker_minggu4_id');
    }

    /**
     * Relasi dengan model Approver untuk minggu 2
     */
    public function approverMinggu2()
    {
        return $this->belongsTo(Approver::class, 'approver_minggu2_id');
    }

    /**
     * Relasi dengan model Approver untuk minggu 4
     */
    public function approverMinggu4()
    {
        return $this->belongsTo(Approver::class, 'approver_minggu4_id');
    }

    /**
     * Method untuk update status berdasarkan approver_minggu2_id dan approver_minggu4_id
     * Status disetujui hanya jika KEDUA minggu sudah disetujui
     */
    private function updateStatus()
    {
        if ($this->isBothWeeksApproved()) {
            $this->status = 'disetujui';
        } else {
            $this->status = 'belum_disetujui';
        }
    }

    /**
     * Helper method untuk mengecek apakah kedua minggu sudah disetujui
     */
    private function isBothWeeksApproved()
    {
        return !empty($this->approver_minggu2_id) && $this->approver_minggu2_id !== null &&
               !empty($this->approver_minggu4_id) && $this->approver_minggu4_id !== null;
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
     * Mutator untuk approver_minggu2_id yang otomatis update status
     */
    public function setApproverMinggu2IdAttribute($value)
    {
        $this->attributes['approver_minggu2_id'] = $value;
        $this->updateStatusFromMutator();
    }

    /**
     * Mutator untuk approver_minggu4_id yang otomatis update status
     */
    public function setApproverMinggu4IdAttribute($value)
    {
        $this->attributes['approver_minggu4_id'] = $value;
        $this->updateStatusFromMutator();
    }

    /**
     * Helper method untuk update status dari mutator
     */
    private function updateStatusFromMutator()
    {
        if ($this->isBothWeeksApprovedFromAttributes()) {
            $this->attributes['status'] = 'disetujui';
        } else {
            $this->attributes['status'] = 'belum_disetujui';
        }
    }

    /**
     * Helper method untuk mengecek approval dari attributes (untuk mutator)
     */
    private function isBothWeeksApprovedFromAttributes()
    {
        return !empty($this->attributes['approver_minggu2_id']) && $this->attributes['approver_minggu2_id'] !== null &&
               !empty($this->attributes['approver_minggu4_id']) && $this->attributes['approver_minggu4_id'] !== null;
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
     * Method helper untuk approval lengkap (kedua minggu)
     */
    public function approveAll($approverMinggu2Id, $approverMinggu4Id)
    {
        $this->approver_minggu2_id = $approverMinggu2Id;
        $this->approver_minggu4_id = $approverMinggu4Id;
        $this->status = 'disetujui';
        $this->save();
        
        return $this;
    }

    /**
     * Method helper untuk approval minggu tertentu
     */
    public function approveWeek($week, $approverId)
    {
        if ($week == 2) {
            $this->approver_minggu2_id = $approverId;
        } elseif ($week == 4) {
            $this->approver_minggu4_id = $approverId;
        }
        
        if ($week == 2 || $week == 4) {
            $this->save();
        }
        
        return $this;
    }

    /**
     * Method helper untuk unapprove semua minggu
     */
    public function unapproveAll()
    {
        $this->approver_minggu2_id = null;
        $this->approver_minggu4_id = null;
        $this->status = 'belum_disetujui';
        $this->save();
        
        return $this;
    }

    /**
     * Method helper untuk unapprove minggu tertentu
     */
    public function unapproveWeek($week)
    {
        if ($week == 2) {
            $this->approver_minggu2_id = null;
        } elseif ($week == 4) {
            $this->approver_minggu4_id = null;
        }
        
        if ($week == 2 || $week == 4) {
            $this->save();
        }
        
        return $this;
    }

    /**
     * Method helper untuk set checker minggu tertentu
     */
    public function setChecker($week, $checkerId)
    {
        if ($week == 2) {
            $this->checker_minggu2_id = $checkerId;
        } elseif ($week == 4) {
            $this->checker_minggu4_id = $checkerId;
        }
        
        if ($week == 2 || $week == 4) {
            $this->save();
        }
        
        return $this;
    }

    /**
     * Check apakah sudah disetujui lengkap (kedua minggu)
     */
    public function isApproved()
    {
        return $this->status === 'disetujui';
    }

    /**
     * Check apakah minggu tertentu sudah disetujui
     */
    public function isWeekApproved($week)
    {
        if ($week == 2) {
            return !empty($this->approver_minggu2_id) && $this->approver_minggu2_id !== null;
        } elseif ($week == 4) {
            return !empty($this->approver_minggu4_id) && $this->approver_minggu4_id !== null;
        }
        return false;
    }

    /**
     * Check apakah minggu tertentu sudah ada checker
     */
    public function isWeekChecked($week)
    {
        if ($week == 2) {
            return !empty($this->checker_minggu2_id) && $this->checker_minggu2_id !== null;
        } elseif ($week == 4) {
            return !empty($this->checker_minggu4_id) && $this->checker_minggu4_id !== null;
        }
        return false;
    }

    /**
     * Get jumlah minggu yang sudah disetujui
     */
    public function getApprovedWeeksCount()
    {
        $count = 0;
        if ($this->isWeekApproved(2)) $count++;
        if ($this->isWeekApproved(4)) $count++;
        return $count;
    }

    /**
     * Get jumlah minggu yang sudah ada checker
     */
    public function getCheckedWeeksCount()
    {
        $count = 0;
        if ($this->isWeekChecked(2)) $count++;
        if ($this->isWeekChecked(4)) $count++;
        return $count;
    }

    /**
     * Get persentase approval
     */
    public function getApprovalPercentage()
    {
        return ($this->getApprovedWeeksCount() / 2) * 100;
    }

    /**
     * Get persentase checked
     */
    public function getCheckedPercentage()
    {
        return ($this->getCheckedWeeksCount() / 2) * 100;
    }

    /**
     * Get list minggu yang belum disetujui
     */
    public function getPendingWeeks()
    {
        $pending = [];
        if (!$this->isWeekApproved(2)) {
            $pending[] = 2;
        }
        if (!$this->isWeekApproved(4)) {
            $pending[] = 4;
        }
        return $pending;
    }

    /**
     * Get list minggu yang belum ada checker
     */
    public function getUncheckedWeeks()
    {
        $unchecked = [];
        if (!$this->isWeekChecked(2)) {
            $unchecked[] = 2;
        }
        if (!$this->isWeekChecked(4)) {
            $unchecked[] = 4;
        }
        return $unchecked;
    }

    /**
     * Get nama checker untuk minggu tertentu
     */
    public function getCheckerName($week)
    {
        if ($week == 2 && $this->checkerMinggu2) {
            return $this->checkerMinggu2->username;
        } elseif ($week == 4 && $this->checkerMinggu4) {
            return $this->checkerMinggu4->username;
        }
        return null;
    }

    /**
     * Get nama approver untuk minggu tertentu
     */
    public function getApproverName($week)
    {
        if ($week == 2 && $this->approverMinggu2) {
            return $this->approverMinggu2->username;
        } elseif ($week == 4 && $this->approverMinggu4) {
            return $this->approverMinggu4->username;
        }
        return null;
    }

    /**
     * Mendapatkan hasil pemeriksaan untuk tabel 1 (minggu 1-2)
     */
    public function hasilTable1()
    {
        return $this->hasMany(VacumCleanerResultsTable1::class, 'check_id');
    }

    /**
     * Mendapatkan hasil pemeriksaan untuk tabel 2 (minggu 3-4)
     */
    public function hasilTable2()
    {
        return $this->hasMany(VacumCleanerResultsTable2::class, 'check_id');
    }
}