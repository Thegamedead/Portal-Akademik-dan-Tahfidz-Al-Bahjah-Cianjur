<?php

namespace App\Services;

use App\DataTransferObjects\AbsensiSessionDTO;
use Illuminate\Support\Collection;

/**
 * Service for validating attendance data
 */
class AbsensiValidationService
{
    /**
     * Validate complete attendance session
     *
     * @param AbsensiSessionDTO $session
     * @param Collection $students
     * @return ValidationResult
     */
    public function validateSession(AbsensiSessionDTO $session, Collection $students): ValidationResult
    {
        // Check for students without setoran
        $missingSetoranIds = $session->getStudentsWithoutSetoran();

        if (!empty($missingSetoranIds)) {
            $studentNames = $students
                ->whereIn('id', $missingSetoranIds)
                ->pluck('nama')
                ->toArray();

            return ValidationResult::failure(
                'Siswa berikut hadir tetapi belum ada setoran: ' . implode(', ', $studentNames)
            );
        }

        // Check if there are any absensis recorded
        if (empty($session->absensis)) {
            return ValidationResult::failure('Tidak ada data absensi yang diisi');
        }

        // Validate waktu mulai is before waktu selesai
        if ($session->waktuMulai->greaterThanOrEqualTo($session->waktuSelesai)) {
            return ValidationResult::failure('Waktu mulai harus lebih awal dari waktu selesai');
        }

        return ValidationResult::success();
    }

    /**
     * Validate that date is not in the future
     *
     * @param \Carbon\Carbon $tanggal
     * @return ValidationResult
     */
    public function validateDateNotFuture(\Carbon\Carbon $tanggal): ValidationResult
    {
        if ($tanggal->isFuture()) {
            return ValidationResult::failure('Tanggal tidak boleh di masa depan');
        }

        return ValidationResult::success();
    }

    /**
     * Validate that date is not too old (e.g., > 30 days)
     *
     * @param \Carbon\Carbon $tanggal
     * @param int $maxDays
     * @return ValidationResult
     */
    public function validateDateNotTooOld(\Carbon\Carbon $tanggal, int $maxDays = 30): ValidationResult
    {
        if ($tanggal->diffInDays(now()) > $maxDays) {
            return ValidationResult::failure("Tanggal tidak boleh lebih dari {$maxDays} hari yang lalu");
        }

        return ValidationResult::success();
    }
}

/**
 * Simple validation result class
 */
class ValidationResult
{
    public function __construct(
        public readonly bool $isValid,
        public readonly ?string $errorMessage = null,
        public readonly array $errors = []
    ) {}

    public static function success(): self
    {
        return new self(true);
    }

    public static function failure(string $message, array $errors = []): self
    {
        return new self(false, $message, $errors);
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage ?? 'Validasi gagal';
    }
}
