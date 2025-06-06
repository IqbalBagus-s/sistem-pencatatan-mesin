<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SlittingCheck extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'slitting_checks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nomer_slitting',
        'bulan',
        'checker_minggu1_id',
        'approver_minggu1_id',
        'checker_minggu2_id',
        'approver_minggu2_id',
        'checker_minggu3_id',
        'approver_minggu3_id',
        'checker_minggu4_id',
        'approver_minggu4_id',
        'status'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
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
     * Relasi dengan model Checker untuk minggu 1
     */
    public function checkerMinggu1()
    {
        return $this->belongsTo(Checker::class, 'checker_minggu1_id');
    }

    /**
     * Relasi dengan model Checker untuk minggu 2
     */
    public function checkerMinggu2()
    {
        return $this->belongsTo(Checker::class, 'checker_minggu2_id');
    }

    /**
     * Relasi dengan model Checker untuk minggu 3
     */
    public function checkerMinggu3()
    {
        return $this->belongsTo(Checker::class, 'checker_minggu3_id');
    }

    /**
     * Relasi dengan model Checker untuk minggu 4
     */
    public function checkerMinggu4()
    {
        return $this->belongsTo(Checker::class, 'checker_minggu4_id');
    }

    /**
     * Relasi dengan model Approver untuk minggu 1
     */
    public function approverMinggu1()
    {
        return $this->belongsTo(Approver::class, 'approver_minggu1_id');
    }

    /**
     * Relasi dengan model Approver untuk minggu 2
     */
    public function approverMinggu2()
    {
        return $this->belongsTo(Approver::class, 'approver_minggu2_id');
    }

    /**
     * Relasi dengan model Approver untuk minggu 3
     */
    public function approverMinggu3()
    {
        return $this->belongsTo(Approver::class, 'approver_minggu3_id');
    }

    /**
     * Relasi dengan model Approver untuk minggu 4
     */
    public function approverMinggu4()
    {
        return $this->belongsTo(Approver::class, 'approver_minggu4_id');
    }

    /**
     * Method untuk update status berdasarkan semua approver_minggu1-4_id
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
        return !empty($this->approver_minggu1_id) && $this->approver_minggu1_id !== null &&
               !empty($this->approver_minggu2_id) && $this->approver_minggu2_id !== null &&
               !empty($this->approver_minggu3_id) && $this->approver_minggu3_id !== null &&
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
     * Mutator untuk approver_minggu1_id yang otomatis update status
     */
    public function setApproverMinggu1IdAttribute($value)
    {
        $this->attributes['approver_minggu1_id'] = $value;
        $this->updateStatusFromMutator();
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
     * Mutator untuk approver_minggu3_id yang otomatis update status
     */
    public function setApproverMinggu3IdAttribute($value)
    {
        $this->attributes['approver_minggu3_id'] = $value;
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
        return !empty($this->attributes['approver_minggu1_id']) && $this->attributes['approver_minggu1_id'] !== null &&
               !empty($this->attributes['approver_minggu2_id']) && $this->attributes['approver_minggu2_id'] !== null &&
               !empty($this->attributes['approver_minggu3_id']) && $this->attributes['approver_minggu3_id'] !== null &&
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
     * Method helper untuk approval lengkap (semua minggu)
     */
    public function approveAll($approverMinggu1Id, $approverMinggu2Id, $approverMinggu3Id, $approverMinggu4Id)
    {
        $this->approver_minggu1_id = $approverMinggu1Id;
        $this->approver_minggu2_id = $approverMinggu2Id;
        $this->approver_minggu3_id = $approverMinggu3Id;
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
        if ($week >= 1 && $week <= 4) {
            $this->{"approver_minggu{$week}_id"} = $approverId;
            $this->save();
        }
        
        return $this;
    }

    /**
     * Method helper untuk unapprove semua minggu
     */
    public function unapproveAll()
    {
        $this->approver_minggu1_id = null;
        $this->approver_minggu2_id = null;
        $this->approver_minggu3_id = null;
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
        if ($week >= 1 && $week <= 4) {
            $this->{"approver_minggu{$week}_id"} = null;
            $this->save();
        }
        
        return $this;
    }

    /**
     * Method helper untuk set checker minggu tertentu
     */
    public function setChecker($week, $checkerId)
    {
        if ($week >= 1 && $week <= 4) {
            $this->{"checker_minggu{$week}_id"} = $checkerId;
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
            $fieldName = "approver_minggu{$week}_id";
            return !empty($this->$fieldName) && $this->$fieldName !== null;
        }
        return false;
    }

    /**
     * Check apakah minggu tertentu sudah ada checker
     */
    public function isWeekChecked($week)
    {
        if ($week >= 1 && $week <= 4) {
            $fieldName = "checker_minggu{$week}_id";
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
     * Get jumlah minggu yang sudah ada checker
     */
    public function getCheckedWeeksCount()
    {
        $count = 0;
        for ($i = 1; $i <= 4; $i++) {
            if ($this->isWeekChecked($i)) {
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
     * Get persentase checked
     */
    public function getCheckedPercentage()
    {
        return ($this->getCheckedWeeksCount() / 4) * 100;
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
     * Get list minggu yang belum ada checker
     */
    public function getUncheckedWeeks()
    {
        $unchecked = [];
        for ($i = 1; $i <= 4; $i++) {
            if (!$this->isWeekChecked($i)) {
                $unchecked[] = $i;
            }
        }
        return $unchecked;
    }

    /**
     * Get nama checker untuk minggu tertentu
     */
    public function getCheckerName($week)
    {
        if ($week >= 1 && $week <= 4) {
            $relationName = "checkerMinggu{$week}";
            if ($this->$relationName) {
                return $this->$relationName->username;
            }
        }
        return null;
    }

    /**
     * Get nama approver untuk minggu tertentu
     */
    public function getApproverName($week)
    {
        if ($week >= 1 && $week <= 4) {
            $relationName = "approverMinggu{$week}";
            if ($this->$relationName) {
                return $this->$relationName->username;
            }
        }
        return null;
    }

    /**
     * Get the results for this slitting check.
     */
    public function results()
    {
        return $this->hasMany(SlittingResult::class, 'check_id');
    }
}