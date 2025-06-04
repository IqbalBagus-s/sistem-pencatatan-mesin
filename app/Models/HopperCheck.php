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
        'checker_id_minggu1',
        'checker_id_minggu2',
        'checker_id_minggu3',
        'checker_id_minggu4',
        // Data approver tiap minggu
        'approver_id_minggu1',
        'approver_id_minggu2',
        'approver_id_minggu3',
        'approver_id_minggu4',
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
     * Method untuk update status berdasarkan semua approver_id_minggu1-4
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
        return !empty($this->approver_id_minggu1) && $this->approver_id_minggu1 !== null &&
               !empty($this->approver_id_minggu2) && $this->approver_id_minggu2 !== null &&
               !empty($this->approver_id_minggu3) && $this->approver_id_minggu3 !== null &&
               !empty($this->approver_id_minggu4) && $this->approver_id_minggu4 !== null;
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
     * Mutator untuk approver_id_minggu1 yang otomatis update status
     */
    public function setApproverIdMinggu1Attribute($value)
    {
        $this->attributes['approver_id_minggu1'] = $value;
        $this->updateStatusFromMutator();
    }

    /**
     * Mutator untuk approver_id_minggu2 yang otomatis update status
     */
    public function setApproverIdMinggu2Attribute($value)
    {
        $this->attributes['approver_id_minggu2'] = $value;
        $this->updateStatusFromMutator();
    }

    /**
     * Mutator untuk approver_id_minggu3 yang otomatis update status
     */
    public function setApproverIdMinggu3Attribute($value)
    {
        $this->attributes['approver_id_minggu3'] = $value;
        $this->updateStatusFromMutator();
    }

    /**
     * Mutator untuk approver_id_minggu4 yang otomatis update status
     */
    public function setApproverIdMinggu4Attribute($value)
    {
        $this->attributes['approver_id_minggu4'] = $value;
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
        return !empty($this->attributes['approver_id_minggu1']) && $this->attributes['approver_id_minggu1'] !== null &&
               !empty($this->attributes['approver_id_minggu2']) && $this->attributes['approver_id_minggu2'] !== null &&
               !empty($this->attributes['approver_id_minggu3']) && $this->attributes['approver_id_minggu3'] !== null &&
               !empty($this->attributes['approver_id_minggu4']) && $this->attributes['approver_id_minggu4'] !== null;
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
        $this->approver_id_minggu1 = $approvedBy1;
        $this->approver_id_minggu2 = $approvedBy2;
        $this->approver_id_minggu3 = $approvedBy3;
        $this->approver_id_minggu4 = $approvedBy4;
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
            $this->{"approver_id_minggu{$week}"} = $approvedBy;
            $this->save();
        }
        
        return $this;
    }

    /**
     * Method helper untuk unapprove semua minggu
     */
    public function unapproveAll()
    {
        $this->approver_id_minggu1 = null;
        $this->approver_id_minggu2 = null;
        $this->approver_id_minggu3 = null;
        $this->approver_id_minggu4 = null;
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
            $this->{"approver_id_minggu{$week}"} = null;
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
            $fieldName = "approver_id_minggu{$week}";
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

    /**
     * Relasi ke model Checker untuk tiap minggu
     */
    public function checkerMinggu1()
    {
        return $this->belongsTo(Checker::class, 'checker_id_minggu1');
    }
    public function checkerMinggu2()
    {
        return $this->belongsTo(Checker::class, 'checker_id_minggu2');
    }
    public function checkerMinggu3()
    {
        return $this->belongsTo(Checker::class, 'checker_id_minggu3');
    }
    public function checkerMinggu4()
    {
        return $this->belongsTo(Checker::class, 'checker_id_minggu4');
    }

    /**
     * Relasi ke model Approver untuk tiap minggu
     */
    public function approverMinggu1()
    {
        return $this->belongsTo(Approver::class, 'approver_id_minggu1');
    }
    public function approverMinggu2()
    {
        return $this->belongsTo(Approver::class, 'approver_id_minggu2');
    }
    public function approverMinggu3()
    {
        return $this->belongsTo(Approver::class, 'approver_id_minggu3');
    }
    public function approverMinggu4()
    {
        return $this->belongsTo(Approver::class, 'approver_id_minggu4');
    }
}