<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * RolloverAuditLog Model
 *
 * Tracks all integrated year-end rollover operations including academic progression,
 * Tahfidz group clearing, and graduation processing. Provides complete audit trail
 * for compliance and troubleshooting.
 *
 * @property int $id
 * @property int $initiated_by Admin ID who initiated the rollover
 * @property int $source_tahun_ajaran_id Previous active academic year
 * @property int $target_tahun_ajaran_id New active academic year
 * @property int $kelas_7_to_8_count Number of students promoted from Kelas 7 to 8
 * @property int $kelas_8_to_9_count Number of students promoted from Kelas 8 to 9
 * @property int $graduated_count Number of students marked as "Lulus SMP"
 * @property int $kelompok_cleared_count Number of students with cleared Kelompok
 * @property string $status Execution status: in_progress, completed, failed, rolled_back
 * @property string|null $error_message Error message if rollover failed
 * @property string|null $error_trace Stack trace if rollover failed
 * @property \Illuminate\Support\Carbon $started_at Rollover start timestamp
 * @property \Illuminate\Support\Carbon|null $completed_at Rollover completion timestamp
 * @property int|null $rolled_back_by Admin ID who performed rollback
 * @property \Illuminate\Support\Carbon|null $rolled_back_at Rollback timestamp
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read Admin $initiatedBy
 * @property-read TahunAjaran $sourceTahunAjaran
 * @property-read TahunAjaran $targetTahunAjaran
 * @property-read Admin|null $rolledBackBy
 * @property-read int $totalAffected Total number of students affected
 */
class RolloverAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'initiated_by',
        'source_tahun_ajaran_id',
        'target_tahun_ajaran_id',
        'kelas_7_to_8_count',
        'kelas_8_to_9_count',
        'graduated_count',
        'kelompok_cleared_count',
        'status',
        'error_message',
        'error_trace',
        'started_at',
        'completed_at',
        'rolled_back_by',
        'rolled_back_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'rolled_back_at' => 'datetime',
            'kelas_7_to_8_count' => 'integer',
            'kelas_8_to_9_count' => 'integer',
            'graduated_count' => 'integer',
            'kelompok_cleared_count' => 'integer',
        ];
    }

    /**
     * Relationship: RolloverAuditLog initiated by Admin
     */
    public function initiatedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'initiated_by');
    }

    /**
     * Relationship: RolloverAuditLog source academic year
     */
    public function sourceTahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'source_tahun_ajaran_id');
    }

    /**
     * Relationship: RolloverAuditLog target academic year
     */
    public function targetTahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'target_tahun_ajaran_id');
    }

    /**
     * Relationship: RolloverAuditLog rolled back by Admin
     */
    public function rolledBackBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'rolled_back_by');
    }

    /**
     * Check if this rollover can be rolled back
     *
     * Rollback is only allowed if:
     * - Status is 'completed'
     * - Completed within last 24 hours
     * - Not already rolled back
     */
    public function canRollback(): bool
    {
        return $this->status === 'completed'
            && $this->completed_at
            && $this->completed_at->diffInHours(now()) <= 24
            && is_null($this->rolled_back_at);
    }

    /**
     * Get total number of students affected by this rollover
     *
     * Includes all academic promotions, graduations, and group clearings
     */
    public function getTotalAffectedAttribute(): int
    {
        return $this->kelas_7_to_8_count
            + $this->kelas_8_to_9_count
            + $this->graduated_count
            + $this->kelompok_cleared_count;
    }

    /**
     * Check if this rollover is currently in progress
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if this rollover completed successfully
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if this rollover failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if this rollover was rolled back
     */
    public function isRolledBack(): bool
    {
        return $this->status === 'rolled_back';
    }
}
