<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CraneMatrasCheck extends Model
{
    use HasFactory, SoftDeletes;

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
        'checked_by',
        'approved_by',
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
     * Method untuk update status berdasarkan approved_by
     * Status disetujui hanya jika approved_by sudah terisi
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
        return !empty($this->approved_by) && $this->approved_by !== null;
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
     * Mutator untuk approved_by yang otomatis update status
     */
    public function setApprovedByAttribute($value)
    {
        $this->attributes['approved_by'] = $value;
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
        return !empty($this->attributes['approved_by']) && $this->attributes['approved_by'] !== null;
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
    public function approve($approvedBy)
    {
        $this->approved_by = $approvedBy;
        $this->status = 'disetujui';
        $this->save();
        
        return $this;
    }

    /**
     * Method helper untuk unapprove
     */
    public function unapprove()
    {
        $this->approved_by = null;
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
     * Check apakah ada approver
     */
    public function hasApprover()
    {
        return !empty($this->approved_by) && $this->approved_by !== null;
    }

    /**
     * Get status approval dalam boolean
     */
    public function getApprovalStatus()
    {
        return $this->hasApprover();
    }

    /**
     * Get informasi lengkap approval
     */
    public function getApprovalInfo()
    {
        return [
            'is_approved' => $this->isApproved(),
            'approved_by' => $this->approved_by,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'approved_at' => $this->updated_at // Asumsi approved saat update terakhir
        ];
    }

    /**
     * Scope untuk filter berdasarkan approver
     */
    public function scopeApprovedBy($query, $approver)
    {
        return $query->where('approved_by', $approver);
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