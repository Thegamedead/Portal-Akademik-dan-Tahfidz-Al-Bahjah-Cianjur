<?php

namespace App\DataTransferObjects;

use Carbon\Carbon;

/**
 * Data Transfer Object for Absensi Session
 *
 * Encapsulates all data for a complete attendance session
 */
readonly class AbsensiSessionDTO
{
    /**
     * @param int $kelompokId
     * @param Carbon $tanggal
     * @param array<int, string> $absensis [siswa_id => status]
     * @param array<int, array<int, array>> $setorans [siswa_id => [entries]]
     * @param int $guruPengisiId
     * @param Carbon $waktuMulai
     * @param Carbon $waktuSelesai
     */
    public function __construct(
        public int $kelompokId,
        public Carbon $tanggal,
        public array $absensis,
        public array $setorans,
        public int $guruPengisiId,
        public Carbon $waktuMulai,
        public Carbon $waktuSelesai,
    ) {}

    /**
     * Get list of siswa IDs who are present
     *
     * @return array<int>
     */
    public function getPresentStudentIds(): array
    {
        return collect($this->absensis)
            ->filter(fn($status) => $status === 'Hadir')
            ->keys()
            ->toArray();
    }

    /**
     * Get students who are present but have no setoran
     *
     * @return array<int>
     */
    public function getStudentsWithoutSetoran(): array
    {
        return collect($this->getPresentStudentIds())
            ->reject(fn($siswaId) =>
                isset($this->setorans[$siswaId]) && !empty($this->setorans[$siswaId])
            )
            ->toArray();
    }

    /**
     * Check if all present students have setoran
     *
     * @return bool
     */
    public function isComplete(): bool
    {
        return empty($this->getStudentsWithoutSetoran());
    }

    /**
     * Get total number of setoran entries
     *
     * @return int
     */
    public function getTotalSetoranCount(): int
    {
        return collect($this->setorans)
            ->flatten(1)
            ->count();
    }
}
