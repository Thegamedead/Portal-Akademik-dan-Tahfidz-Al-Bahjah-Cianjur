<?php

namespace App\DataTransferObjects;

/**
 * Data Transfer Object for Academic Progression Results
 *
 * Encapsulates the outcome of academic promotion operations during rollover
 */
readonly class ProgressionResult
{
    /**
     * @param int $kelas7To8Count Number of students promoted from Kelas 7 to Kelas 8
     * @param int $kelas8To9Count Number of students promoted from Kelas 8 to Kelas 9
     * @param int $graduatedCount Number of students marked as "Lulus SMP"
     * @param array<int> $promotedStudentIds IDs of all promoted students
     * @param array<int> $graduatedStudentIds IDs of all graduated students
     */
    public function __construct(
        public int $kelas7To8Count,
        public int $kelas8To9Count,
        public int $graduatedCount,
        public array $promotedStudentIds,
        public array $graduatedStudentIds,
    ) {}

    /**
     * Get total number of students affected by academic progression
     *
     * @return int
     */
    public function getTotalAffected(): int
    {
        return $this->kelas7To8Count + $this->kelas8To9Count + $this->graduatedCount;
    }

    /**
     * Get all student IDs affected by any progression operation
     *
     * @return array<int>
     */
    public function getAllAffectedStudentIds(): array
    {
        return array_unique(array_merge($this->promotedStudentIds, $this->graduatedStudentIds));
    }

    /**
     * Check if any students were processed
     *
     * @return bool
     */
    public function hasAffectedStudents(): bool
    {
        return $this->getTotalAffected() > 0;
    }
}
