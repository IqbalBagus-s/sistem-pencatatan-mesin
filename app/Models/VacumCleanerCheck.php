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
        'checker_minggu2',
        'checker_minggu4',
        'approver_minggu2',
        'approver_minggu4',
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
     * Method untuk update status berdasarkan approver_minggu2 dan approver_minggu4
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
        return !empty($this->approver_minggu2) && $this->approver_minggu2 !== null &&
               !empty($this->approver_minggu4) && $this->approver_minggu4 !== null;
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
     * Mutator untuk approver_minggu2 yang otomatis update status
     */
    public function setApproverMinggu2Attribute($value)
    {
        $this->attributes['approver_minggu2'] = $value;
        $this->updateStatusFromMutator();
    }

    /**
     * Mutator untuk approver_minggu4 yang otomatis update status
     */
    public function setApproverMinggu4Attribute($value)
    {
        $this->attributes['approver_minggu4'] = $value;
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
        return !empty($this->attributes['approver_minggu2']) && $this->attributes['approver_minggu2'] !== null &&
               !empty($this->attributes['approver_minggu4']) && $this->attributes['approver_minggu4'] !== null;
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
    public function approveAll($approverMinggu2, $approverMinggu4)
    {
        $this->approver_minggu2 = $approverMinggu2;
        $this->approver_minggu4 = $approverMinggu4;
        $this->status = 'disetujui';
        $this->save();
        
        return $this;
    }

    /**
     * Method helper untuk approval minggu tertentu
     */
    public function approveWeek($week, $approvedBy)
    {
        if ($week == 2) {
            $this->approver_minggu2 = $approvedBy;
        } elseif ($week == 4) {
            $this->approver_minggu4 = $approvedBy;
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
        $this->approver_minggu2 = null;
        $this->approver_minggu4 = null;
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
            $this->approver_minggu2 = null;
        } elseif ($week == 4) {
            $this->approver_minggu4 = null;
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
            return !empty($this->approver_minggu2) && $this->approver_minggu2 !== null;
        } elseif ($week == 4) {
            return !empty($this->approver_minggu4) && $this->approver_minggu4 !== null;
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
     * Get persentase approval
     */
    public function getApprovalPercentage()
    {
        return ($this->getApprovedWeeksCount() / 2) * 100;
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