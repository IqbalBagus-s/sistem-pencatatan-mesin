<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Hashidable;

class CraneMatrasCheck extends Model
{
    use HasFactory, SoftDeletes, Hashidable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'crane_matras_checks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nomer_crane_matras',
        'bulan',
        'tanggal',
        'checker_id',
        'approver_id',
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
     * Relasi dengan model Checker
     */
    public function checker()
    {
        return $this->belongsTo(Checker::class, 'checker_id');
    }

    /**
     * Relasi dengan model Approver
     */
    public function approver()
    {
        return $this->belongsTo(Approver::class, 'approver_id');
    }

    /**
     * Method untuk update status berdasarkan approver_id
     * Status disetujui hanya jika approver_id sudah terisi
     */
    private function updateStatus()
    {
        if ($this->isApproved()) {
            $this->status = 'disetujui';
        } else {
            $this->status = 'belum_disetujui';
        }
    }

    /**
     * Helper method untuk mengecek apakah sudah disetujui
     */
    private function isApprovedByFilled()
    {
        return !empty($this->approver_id) && $this->approver_id !== null;
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
     * Mutator untuk approver_id yang otomatis update status
     */
    public function setApproverIdAttribute($value)
    {
        $this->attributes['approver_id'] = $value;
        $this->updateStatusFromMutator();
    }

    /**
     * Helper method untuk update status dari mutator
     */
    private function updateStatusFromMutator()
    {
        if ($this->isApprovedFromAttributes()) {
            $this->attributes['status'] = 'disetujui';
        } else {
            $this->attributes['status'] = 'belum_disetujui';
        }
    }

    /**
     * Helper method untuk mengecek approval dari attributes (untuk mutator)
     */
    private function isApprovedFromAttributes()
    {
        return !empty($this->attributes['approver_id']) && $this->attributes['approver_id'] !== null;
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
     * Method helper untuk approval
     */
    public function approve($approverId)
    {
        $this->approver_id = $approverId;
        $this->status = 'disetujui';
        $this->save();
        
        return $this;
    }

    /**
     * Method helper untuk unapprove
     */
    public function unapprove()
    {
        $this->approver_id = null;
        $this->status = 'belum_disetujui';
        $this->save();
        
        return $this;
    }

    /**
     * Method helper untuk set checker
     */
    public function setChecker($checkerId)
    {
        $this->checker_id = $checkerId;
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
     * Check apakah ada approver
     */
    public function hasApprover()
    {
        return !empty($this->approver_id) && $this->approver_id !== null;
    }

    /**
     * Check apakah ada checker
     */
    public function hasChecker()
    {
        return !empty($this->checker_id) && $this->checker_id !== null;
    }

    /**
     * Get status approval dalam boolean
     */
    public function getApprovalStatus()
    {
        return $this->hasApprover();
    }

    /**
     * Get nama checker
     */
    public function getCheckerName()
    {
        return $this->checker ? $this->checker->username : null;
    }

    /**
     * Get nama approver
     */
    public function getApproverName()
    {
        return $this->approver ? $this->approver->username : null;
    }

    /**
     * Get informasi lengkap approval
     */
    public function getApprovalInfo()
    {
        return [
            'is_approved' => $this->isApproved(),
            'approver_id' => $this->approver_id,
            'approver_name' => $this->getApproverName(),
            'checker_id' => $this->checker_id,
            'checker_name' => $this->getCheckerName(),
            'status' => $this->status,
            'status_label' => $this->status_label,
            'approved_at' => $this->updated_at // Asumsi approved saat update terakhir
        ];
    }

    /**
     * Scope untuk filter berdasarkan approver
     */
    public function scopeApprovedBy($query, $approverId)
    {
        return $query->where('approver_id', $approverId);
    }

    /**
     * Scope untuk filter berdasarkan checker
     */
    public function scopeCheckedBy($query, $checkerId)
    {
        return $query->where('checker_id', $checkerId);
    }

    /**
     * Scope untuk filter berdasarkan bulan
     */
    public function scopeByMonth($query, $month)
    {
        return $query->where('bulan', $month);
    }

    /**
     * Scope untuk filter berdasarkan nomor crane
     */
    public function scopeByNomer($query, $nomer)
    {
        return $query->where('nomer_crane_matras', $nomer);
    }

    /**
     * Get the results associated with the check.
     */
    public function results()
    {
        return $this->hasMany(CraneMatrasResult::class, 'check_id');
    }
}