<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatGuruPembimbing extends Model
{
    protected $fillable = [
        'kelompok_id',
        'guru_dari_id',
        'guru_ke_id',
        'tanggal_efektif',
        'alasan',
        'admin_id',
    ];

    protected $casts = [
        'tanggal_efektif' => 'date',
    ];

    /**
     * Kelompok yang mengalami perubahan guru.
     */
    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class);
    }

    /**
     * Guru sebelumnya (termasuk yang sudah dihapus/nonaktif).
     */
    public function guruDari(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_dari_id')->withTrashed();
    }

    /**
     * Guru baru (termasuk yang sudah dihapus/nonaktif).
     */
    public function guruKe(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_ke_id')->withTrashed();
    }

    /**
     * Admin yang melakukan perubahan.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Scope untuk mengurutkan dari terbaru.
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('tanggal_efektif');
    }
}
