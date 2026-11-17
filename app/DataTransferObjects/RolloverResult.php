<?php

namespace App\DataTransferObjects;

/**
 * Data Transfer Object for Rollover Execution Results
 *
 * Encapsulates the complete outcome of a rollover operation
 */
readonly class RolloverResult
{
    /**
     * @param bool $success Whether the rollover completed successfully
     * @param int $rolloverLogId ID of the RolloverAuditLog entry created
     * @param int $sourceTahunAjaranId ID of the previous active Tahun Ajaran
     * @param int $targetTahunAjaranId ID of the newly active Tahun Ajaran
     * @param string $targetTahunAjaranLabel Label of the new academic year
     * @param ProgressionResult $academicResult Academic progression operation results
     * @param TahfidzClearResult $tahfidzResult Tahfidz group clearing operation results
     * @param string|null $errorMessage Error message if rollover failed
     * @param \DateTime $executedAt Timestamp when rollover was executed
     */
    public function __construct(
        public bool $success,
        public int $rolloverLogId,
        public int $sourceTahunAjaranId,
        public int $targetTahunAjaranId,
        public string $targetTahunAjaranLabel,
        public ProgressionResult $academicResult,
        public TahfidzClearResult $tahfidzResult,
        public ?string $errorMessage = null,
        public \DateTime $executedAt = new \DateTime(),
    ) {}

    /**
     * Get total number of students affected by rollover
     *
     * @return int
     */
    public function getTotalAffected(): int
    {
        return $this->academicResult->getTotalAffected()
            + $this->tahfidzResult->clearedCount;
    }

    /**
     * Check if rollover had any impact
     *
     * @return bool
     */
    public function hasImpact(): bool
    {
        return $this->getTotalAffected() > 0;
    }

    /**
     * Create a successful result instance
     *
     * @param int $rolloverLogId
     * @param int $sourceTahunAjaranId
     * @param int $targetTahunAjaranId
     * @param string $targetTahunAjaranLabel
     * @param ProgressionResult $academicResult
     * @param TahfidzClearResult $tahfidzResult
     * @return self
     */
    public static function success(
        int $rolloverLogId,
        int $sourceTahunAjaranId,
        int $targetTahunAjaranId,
        string $targetTahunAjaranLabel,
        ProgressionResult $academicResult,
        TahfidzClearResult $tahfidzResult
    ): self {
        return new self(
            success: true,
            rolloverLogId: $rolloverLogId,
            sourceTahunAjaranId: $sourceTahunAjaranId,
            targetTahunAjaranId: $targetTahunAjaranId,
            targetTahunAjaranLabel: $targetTahunAjaranLabel,
            academicResult: $academicResult,
            tahfidzResult: $tahfidzResult,
            errorMessage: null,
            executedAt: new \DateTime(),
        );
    }

    /**
     * Create a failed result instance
     *
     * @param int $rolloverLogId
     * @param int $sourceTahunAjaranId
     * @param int $targetTahunAjaranId
     * @param string $errorMessage
     * @return self
     */
    public static function failure(
        int $rolloverLogId,
        int $sourceTahunAjaranId,
        int $targetTahunAjaranId,
        string $errorMessage
    ): self {
        return new self(
            success: false,
            rolloverLogId: $rolloverLogId,
            sourceTahunAjaranId: $sourceTahunAjaranId,
            targetTahunAjaranId: $targetTahunAjaranId,
            targetTahunAjaranLabel: '',
            academicResult: new ProgressionResult(0, 0, 0, [], []),
            tahfidzResult: new TahfidzClearResult(0, [], 0),
            errorMessage: $errorMessage,
            executedAt: new \DateTime(),
        );
    }

    /**
     * Convert to array for JSON serialization
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'rollover_log_id' => $this->rolloverLogId,
            'source_tahun_ajaran_id' => $this->sourceTahunAjaranId,
            'target_tahun_ajaran_id' => $this->targetTahunAjaranId,
            'target_tahun_ajaran_label' => $this->targetTahunAjaranLabel,
            'academic_result' => [
                'kelas_7_to_8_count' => $this->academicResult->kelas7To8Count,
                'kelas_8_to_9_count' => $this->academicResult->kelas8To9Count,
                'graduated_count' => $this->academicResult->graduatedCount,
                'total_affected' => $this->academicResult->getTotalAffected(),
            ],
            'tahfidz_result' => [
                'cleared_count' => $this->tahfidzResult->clearedCount,
                'riwayat_records_created' => $this->tahfidzResult->riwayatRecordsCreated,
            ],
            'total_affected' => $this->getTotalAffected(),
            'error_message' => $this->errorMessage,
            'executed_at' => $this->executedAt->format('Y-m-d H:i:s'),
        ];
    }
}
