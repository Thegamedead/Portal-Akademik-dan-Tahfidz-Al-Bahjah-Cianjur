<?php

namespace App\DataTransferObjects;

/**
 * Data Transfer Object for Pre-Rollover Impact Summary
 *
 * Provides counts of students that will be affected by rollover operations
 * Used for confirmation dialog display before execution
 */
readonly class ImpactSummary
{
    /**
     * @param int $currentTahunAjaranId ID of currently active Tahun Ajaran
     * @param int $targetTahunAjaranId ID of target Tahun Ajaran
     * @param string $currentTahunAjaranLabel Label of current academic year (e.g., "2024-2025")
     * @param string $targetTahunAjaranLabel Label of target academic year
     * @param int $kelas7To8Count Students to be promoted from Kelas 7 to 8
     * @param int $kelas8To9Count Students to be promoted from Kelas 8 to 9
     * @param int $graduatedCount Students to be marked as "Lulus SMP"
     * @param int $kelompokClearedCount Students whose Kelompok will be cleared
     */
    public function __construct(
        public int $currentTahunAjaranId,
        public int $targetTahunAjaranId,
        public string $currentTahunAjaranLabel,
        public string $targetTahunAjaranLabel,
        public int $kelas7To8Count,
        public int $kelas8To9Count,
        public int $graduatedCount,
        public int $kelompokClearedCount,
    ) {}

    /**
     * Get total number of students that will be affected
     *
     * @return int
     */
    public function getTotalAffected(): int
    {
        return $this->kelas7To8Count
            + $this->kelas8To9Count
            + $this->graduatedCount
            + $this->kelompokClearedCount;
    }

    /**
     * Get total academic progression changes (promotions + graduations)
     *
     * @return int
     */
    public function getTotalAcademicChanges(): int
    {
        return $this->kelas7To8Count + $this->kelas8To9Count + $this->graduatedCount;
    }

    /**
     * Check if any students will be affected
     *
     * @return bool
     */
    public function hasAffectedStudents(): bool
    {
        return $this->getTotalAffected() > 0;
    }

    /**
     * Check if Tahun Ajaran is actually changing
     *
     * @return bool
     */
    public function isDifferentTahunAjaran(): bool
    {
        return $this->currentTahunAjaranId !== $this->targetTahunAjaranId;
    }

    /**
     * Convert to array for JSON serialization
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'current_tahun_ajaran_id' => $this->currentTahunAjaranId,
            'target_tahun_ajaran_id' => $this->targetTahunAjaranId,
            'current_tahun_ajaran_label' => $this->currentTahunAjaranLabel,
            'target_tahun_ajaran_label' => $this->targetTahunAjaranLabel,
            'kelas_7_to_8_count' => $this->kelas7To8Count,
            'kelas_8_to_9_count' => $this->kelas8To9Count,
            'graduated_count' => $this->graduatedCount,
            'kelompok_cleared_count' => $this->kelompokClearedCount,
            'total_affected' => $this->getTotalAffected(),
        ];
    }
}
