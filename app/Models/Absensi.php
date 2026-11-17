<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\StatusKehadiran;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensis';

    protected $fillable = [
        'kelompok_id',
        'siswa_id',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'status_kehadiran',
        'guru_pengisi_id',
        'alfa_berturut_turut',
        'override_admin',
        'admin_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'status_kehadiran' => StatusKehadiran::class,
        'override_admin' => 'boolean',
        'alfa_berturut_turut' => 'integer',
    ];

    // Relationships

    /**
     * Get the kelompok that owns the absensi
     */
    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(Kelompok::class);
    }

    /**
     * Get the siswa that owns the absensi
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * Get the guru who filled the absensi
     */
    public function guruPengisi(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_pengisi_id');
    }

    /**
     * Get the admin who overrode the absensi (nullable)
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class)->withDefault();
    }

    /**
     * Get all setorans for this absensi
     */
    public function setorans(): HasMany
    {
        return $this->hasMany(Setoran::class);
    }

    // Business Logic Methods

    /**
     * Check if this absensi can be edited by Guru
     * Req 12: Guru can edit until 23:59:59 on the same day
     */
    public function canEditByGuru(): bool
    {
        return $this->tanggal->isToday() && now()->endOfDay()->isFuture();
    }

    /**
     * Update Alfa counter based on status change
     * Req 14: Increment on Alfa, reset on Hadir/Izin/Sakit
     */
    public function updateAlfaCounter(): void
    {
        if ($this->status_kehadiran === StatusKehadiran::Alfa) {
            // Get previous absensi for this siswa in same kelompok
            $previous = static::where('kelompok_id', $this->kelompok_id)
                ->where('siswa_id', $this->siswa_id)
                ->where('tanggal', '<', $this->tanggal)
                ->orderBy('tanggal', 'desc')
                ->first();

            $this->alfa_berturut_turut = $previous ? $previous->alfa_berturut_turut + 1 : 1;
        } else {
            // Reset counter on Hadir/Izin/Sakit
            $this->alfa_berturut_turut = 0;
        }
    }

    /**
     * Check if alfa threshold is reached (for notification trigger)
     * Req 14: Compare against configured threshold
     */
    public function hasReachedAlfaThreshold(): bool
    {
        $threshold = config('tahfidz.alfa_berturut_turut_threshold', 3);
        return $this->alfa_berturut_turut >= $threshold;
    }

    /**
     * Mark as admin override
     * Req 13: Record admin modification
     */
    public function markAsOverride(int $adminId): void
    {
        $this->override_admin = true;
        $this->admin_id = $adminId;
    }
}
