<?php

namespace App\DataTransferObjects;

/**
 * Data Transfer Object for Tahfidz Group Clearing Results
 *
 * Encapsulates the outcome of Kelompok assignment clearing during rollover
 */
readonly class TahfidzClearResult
{
    /**
     * @param int $clearedCount Number of students whose Kelompok assignments were cleared
     * @param array<int> $clearedStudentIds IDs of students whose groups were cleared
     * @param int $riwayatRecordsCreated Number of RiwayatKelompok audit records created
     */
    public function __construct(
        public int $clearedCount,
        public array $clearedStudentIds,
        public int $riwayatRecordsCreated,
    ) {}

    /**
     * Check if any students were affected
     *
     * @return bool
     */
    public function hasAffectedStudents(): bool
    {
        return $this->clearedCount > 0;
    }

    /**
     * Verify that audit trail matches affected students
     *
     * @return bool
     */
    public function isAuditTrailComplete(): bool
    {
        return $this->riwayatRecordsCreated === $this->clearedCount;
    }
}
