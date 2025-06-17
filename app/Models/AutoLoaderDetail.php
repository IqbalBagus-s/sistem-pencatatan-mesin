<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AutoloaderDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'autoloader_details';

    protected $fillable = [
        'tanggal_check_id',
        'tanggal',  
        'checker_id',
        'approver_id',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Konstanta untuk status
    const STATUS_BELUM_DISETUJUI = 'belum_disetujui';
    const STATUS_DISETUJUI = 'disetujui';

    /**
     * Boot method untuk event model
     */
    protected static function boot()
    {
        parent::boot();

        // Event saat model dibuat
        static::creating(function ($model) {
            if (empty($model->status)) {
                $model->status = self::STATUS_BELUM_DISETUJUI;
            }
        });

        // Event saat model diupdate
        static::updating(function ($model) {
            $model->updateStatusBasedOnApproval();
        });

        // Event saat model disimpan (create atau update)
        static::saving(function ($model) {
            $model->updateStatusBasedOnApproval();
        });
    }

    /**
     * Update status berdasarkan approver_id
     */
    public function updateStatusBasedOnApproval()
    {
        if (!empty($this->approver_id) && $this->approver_id !== null) {
            $this->status = self::STATUS_DISETUJUI;
        } else {
            $this->status = self::STATUS_BELUM_DISETUJUI;
        }
    }

    /**
     * Scope untuk data yang belum disetujui
     */
    public function scopeBelumDisetujui($query)
    {
        return $query->where('status', self::STATUS_BELUM_DISETUJUI);
    }

    /**
     * Scope untuk menghitung tanggal_check_id yang memiliki status belum disetujui
     * Mengembalikan count berdasarkan unique tanggal_check_id, bukan jumlah record
     */
    public function scopeBelumDisetujuiGrouped($query)
    {
        return $query->where('status', self::STATUS_BELUM_DISETUJUI)
                    ->distinct('tanggal_check_id');
    }

    /**
     * Static method untuk mendapatkan jumlah tanggal_check_id yang belum disetujui
     * Digunakan khusus untuk notifikasi dashboard
     */
    public static function countBelumDisetujuiGrouped()
    {
        return self::where('status', self::STATUS_BELUM_DISETUJUI)
                   ->distinct('tanggal_check_id')
                   ->count('tanggal_check_id');
    }

    /**
     * Static method untuk mendapatkan daftar tanggal_check_id yang belum disetujui
     */
    public static function getTanggalCheckIdBelumDisetujui()
    {
        return self::where('status', self::STATUS_BELUM_DISETUJUI)
                   ->distinct()
                   ->pluck('tanggal_check_id')
                   ->toArray();
    }

    /**
     * Scope untuk data yang sudah disetujui
     */
    public function scopeDisetujui($query)
    {
        return $query->where('status', self::STATUS_DISETUJUI);
    }

    /**
     * Mutator untuk approver_id - otomatis update status
     */
    public function setApproverIdAttribute($value)
    {
        $this->attributes['approver_id'] = $value;
        
        // Update status berdasarkan approver_id
        if (!empty($value) && $value !== null) {
            $this->attributes['status'] = self::STATUS_DISETUJUI;
        } else {
            $this->attributes['status'] = self::STATUS_BELUM_DISETUJUI;
        }
    }

    /**
     * Accessor untuk status dalam bahasa Indonesia
     */
    public function getStatusLabelAttribute()
    {
        return $this->status === self::STATUS_DISETUJUI ? 'Disetujui' : 'Belum Disetujui';
    }

    /**
     * Method untuk mengubah status menjadi disetujui
     */
    public function approve($approvedBy = null)
    {
        $this->approver_id = $approvedBy;
        $this->status = self::STATUS_DISETUJUI;
        return $this->save();
    }

    /**
     * Method untuk mengubah status menjadi belum disetujui
     */
    public function unapprove()
    {
        $this->approver_id = null;
        $this->status = self::STATUS_BELUM_DISETUJUI;
        return $this->save();
    }

    /**
     * Check apakah data sudah disetujui
     */
    public function isApproved()
    {
        return $this->status === self::STATUS_DISETUJUI;
    }

    /**
     * Check apakah data belum disetujui
     */
    public function isPending()
    {
        return $this->status === self::STATUS_BELUM_DISETUJUI;
    }

    /**
     * Get the autoloader check that owns this checker and approver.
     */
    public function autoloaderCheck()
    {
        return $this->belongsTo(AutoloaderCheck::class, 'tanggal_check_id');
    }

    /**
     * Relasi ke Checker
     */
    public function checker()
    {
        return $this->belongsTo(Checker::class, 'checker_id');
    }

    /**
     * Relasi ke Approver
     */
    public function approver()
    {
        return $this->belongsTo(Approver::class, 'approver_id');
    }
}