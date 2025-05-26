<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HopperCheck extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hopper_checks';

    protected $fillable = [
        'nomer_hopper',
        'bulan',
        // Data tanggal tiap minggu
        'tanggal_minggu1',
        'tanggal_minggu2',
        'tanggal_minggu3',
        'tanggal_minggu4',
        // Data checker tiap minggu
        'checked_by_minggu1',
        'checked_by_minggu2',
        'checked_by_minggu3',
        'checked_by_minggu4',
        // Data approver tiap minggu
        'approved_by_minggu1',
        'approved_by_minggu2',
        'approved_by_minggu3',
        'approved_by_minggu4',
        'status'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'tanggal_minggu1',
        'tanggal_minggu2',
        'tanggal_minggu3',
        'tanggal_minggu4'
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
     * Method untuk update status berdasarkan semua approved_by_minggu1-4
     * Status disetujui hanya jika SEMUA minggu sudah disetujui
     */
    private function updateStatus()
    {
        if ($this->isAllWeeksApproved()) {
            $this->status = 'disetujui';
        } else {
            $this->status = 'belum_disetujui';
        }
    }

    /**
     * Helper method untuk mengecek apakah semua minggu sudah disetujui
     */
    private function isAllWeeksApproved()
    {
        return !empty($this->approved_by_minggu1) && $this->approved_by_minggu1 !== null &&
               !empty($this->approved_by_minggu2) && $this->approved_by_minggu2 !== null &&
               !empty($this->approved_by_minggu3) && $this->approved_by_minggu3 !== null &&
               !empty($this->approved_by_minggu4) && $this->approved_by_minggu4 !== null;
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
     * Mutator untuk approved_by_minggu1 yang otomatis update status
     */
    public function setApprovedByMinggu1Attribute($value)
    {
        $this->attributes['approved_by_minggu1'] = $value;
        $this->updateStatusFromMutator();
    }

    /**
     * Mutator untuk approved_by_minggu2 yang otomatis update status
     */
    public function setApprovedByMinggu2Attribute($value)
    {
        $this->attributes['approved_by_minggu2'] = $value;
        $this->updateStatusFromMutator();
    }

    /**
     * Mutator untuk approved_by_minggu3 yang otomatis update status
     */
    public function setApprovedByMinggu3Attribute($value)
    {
        $this->attributes['approved_by_minggu3'] = $value;
        $this->updateStatusFromMutator();
    }

    /**
     * Mutator untuk approved_by_minggu4 yang otomatis update status
     */
    public function setApprovedByMinggu4Attribute($value)
    {
        $this->attributes['approved_by_minggu4'] = $value;
        $this->updateStatusFromMutator();
    }

    /**
     * Helper method untuk update status dari mutator
     */
    private function updateStatusFromMutator()
    {
        if ($this->isAllWeeksApprovedFromAttributes()) {
            $this->attributes['status'] = 'disetujui';
        } else {
            $this->attributes['status'] = 'belum_disetujui';
        }
    }

    /**
     * Helper method untuk mengecek approval dari attributes (untuk mutator)
     */
    private function isAllWeeksApprovedFromAttributes()
    {
        return !empty($this->attributes['approved_by_minggu1']) && $this->attributes['approved_by_minggu1'] !== null &&
               !empty($this->attributes['approved_by_minggu2']) && $this->attributes['approved_by_minggu2'] !== null &&
               !empty($this->attributes['approved_by_minggu3']) && $this->attributes['approved_by_minggu3'] !== null &&
               !empty($this->attributes['approved_by_minggu4']) && $this->attributes['approved_by_minggu4'] !== null;
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
     * Method helper untuk approval lengkap (semua minggu)
     */
    public function approveAll($approvedBy1, $approvedBy2, $approvedBy3, $approvedBy4)
    {
        $this->approved_by_minggu1 = $approvedBy1;
        $this->approved_by_minggu2 = $approvedBy2;
        $this->approved_by_minggu3 = $approvedBy3;
        $this->approved_by_minggu4 = $approvedBy4;
        $this->status = 'disetujui';
        $this->save();
        
        return $this;
    }

    /**
     * Method helper untuk approval minggu tertentu
     */
    public function approveWeek($week, $approvedBy)
    {
        if ($week >= 1 && $week <= 4) {
            $this->{"approved_by_minggu{$week}"} = $approvedBy;
            $this->save();
        }
        
        return $this;
    }

    /**
     * Method helper untuk unapprove semua minggu
     */
    public function unapproveAll()
    {
        $this->approved_by_minggu1 = null;
        $this->approved_by_minggu2 = null;
        $this->approved_by_minggu3 = null;
        $this->approved_by_minggu4 = null;
        $this->status = 'belum_disetujui';
        $this->save();
        
        return $this;
    }

    /**
     * Method helper untuk unapprove minggu tertentu
     */
    public function unapproveWeek($week)
    {
        if ($week >= 1 && $week <= 4) {
            $this->{"approved_by_minggu{$week}"} = null;
            $this->save();
        }
        
        return $this;
    }

    /**
     * Check apakah sudah disetujui lengkap (semua minggu)
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
        if ($week >= 1 && $week <= 4) {
            $fieldName = "approved_by_minggu{$week}";
            return !empty($this->$fieldName) && $this->$fieldName !== null;
        }
        return false;
    }

    /**
     * Get jumlah minggu yang sudah disetujui
     */
    public function getApprovedWeeksCount()
    {
        $count = 0;
        for ($i = 1; $i <= 4; $i++) {
            if ($this->isWeekApproved($i)) {
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
        return ($this->getApprovedWeeksCount() / 4) * 100;
    }

    /**
     * Get list minggu yang belum disetujui
     */
    public function getPendingWeeks()
    {
        $pending = [];
        for ($i = 1; $i <= 4; $i++) {
            if (!$this->isWeekApproved($i)) {
                $pending[] = $i;
            }
        }
        return $pending;
    }

    /**
     * Relasi dengan tabel hopper_results (one-to-many).
     */
    public function results()
    {
        return $this->hasMany(HopperResult::class, 'check_id');
    }
}