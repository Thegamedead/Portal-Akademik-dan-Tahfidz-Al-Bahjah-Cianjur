<?php

namespace App\Services\Handlers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Enums\StatusAkademik;
use App\DataTransferObjects\ProgressionResult;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Academic Progression Handler
 *
 * Handles academic promotion and graduation operations during year-end rollover.
 * Processes:
 * - Kelas 7 → Kelas 8 promotion
 * - Kelas 8 → Kelas 9 promotion
 * - Kelas 9 → "Lulus SMP" graduation
 */
class AcademicProgressionHandler
{
    /**
     * Process academic progression for all active students
     *
     * Promotes:
     * - Kelas 7 students → Kelas 8
     * - Kelas 8 students → Kelas 9
     * - Kelas 9 students → Status "Lulus SMP"
     *
     * @return ProgressionResult Contains counts and affected student IDs
     */
    public function processPromotion(): ProgressionResult
    {
        $promotedStudentIds = [];
        $graduatedStudentIds = [];

        // Get all Kelas levels
        $kelas7 = Kelas::where('tingkat', '7')->get();
        $kelas8 = Kelas::where('tingkat', '8')->get();
        $kelas9 = Kelas::where('tingkat', '9')->get();

        // Validate that all required Kelas levels exist
        if ($kelas7->isEmpty() || $kelas8->isEmpty() || $kelas9->isEmpty()) {
            throw new \RuntimeException('Data kelas tidak lengkap: semua tingkat (7, 8, 9) harus tersedia');
        }

        // Track counts for each promotion level
        $kelas7To8Count = 0;
        $kelas8To9Count = 0;
        $graduatedCount = 0;

        // Process Kelas 7 → Kelas 8 promotions
        foreach ($kelas7 as $sourceKelas) {
            $targetKelas = $sourceKelas->getNextGradeKelas();

            if ($targetKelas) {
                $count = $this->promoteKelas($sourceKelas->id, $targetKelas->id);
                $kelas7To8Count += $count;

                // Collect promoted student IDs
                $promotedIds = Siswa::where('kelas_id', $targetKelas->id)
                    ->where('status_akademik', StatusAkademik::AKTIF->value)
                    ->pluck('id')
                    ->toArray();

                $promotedStudentIds = array_merge($promotedStudentIds, $promotedIds);
            } else {
                Log::warning("No target Kelas found for promotion from {$sourceKelas->nama_kelas}");
            }
        }

        // Process Kelas 8 → Kelas 9 promotions
        foreach ($kelas8 as $sourceKelas) {
            $targetKelas = $sourceKelas->getNextGradeKelas();

            if ($targetKelas) {
                $count = $this->promoteKelas($sourceKelas->id, $targetKelas->id);
                $kelas8To9Count += $count;

                // Collect promoted student IDs
                $promotedIds = Siswa::where('kelas_id', $targetKelas->id)
                    ->where('status_akademik', StatusAkademik::AKTIF->value)
                    ->pluck('id')
                    ->toArray();

                $promotedStudentIds = array_merge($promotedStudentIds, $promotedIds);
            } else {
                Log::warning("No target Kelas found for promotion from {$sourceKelas->nama_kelas}");
            }
        }

        // Process Kelas 9 → Graduation
        $graduationDate = Carbon::now();
        foreach ($kelas9 as $kelas9Instance) {
            $count = $this->graduateStudents($kelas9Instance->id, $graduationDate);
            $graduatedCount += $count;

            // Collect graduated student IDs
            $graduatedIds = Siswa::where('kelas_id', $kelas9Instance->id)
                ->where('status_akademik', StatusAkademik::LULUS_SMP->value)
                ->pluck('id')
                ->toArray();

            $graduatedStudentIds = array_merge($graduatedStudentIds, $graduatedIds);
        }

        Log::info('Academic progression completed', [
            'kelas_7_to_8' => $kelas7To8Count,
            'kelas_8_to_9' => $kelas8To9Count,
            'graduated' => $graduatedCount,
        ]);

        return new ProgressionResult(
            kelas7To8Count: $kelas7To8Count,
            kelas8To9Count: $kelas8To9Count,
            graduatedCount: $graduatedCount,
            promotedStudentIds: array_unique($promotedStudentIds),
            graduatedStudentIds: array_unique($graduatedStudentIds),
        );
    }

    /**
     * Promote students from one Kelas to the next
     *
     * Uses bulk update query for performance optimization.
     * Only promotes students with status_akademik = 'Aktif'.
     *
     * @param int $fromKelasId Source Kelas ID
     * @param int $toKelasId Target Kelas ID
     * @return int Count of students promoted
     */
    private function promoteKelas(int $fromKelasId, int $toKelasId): int
    {
        // Get eligible students
        $eligibleStudents = Siswa::eligibleForPromotion($fromKelasId)->get();

        // Validate eligibility for each student
        $validStudentIds = $eligibleStudents->filter(function (Siswa $siswa) {
            return $this->canPromote($siswa);
        })->pluck('id')->toArray();

        if (empty($validStudentIds)) {
            return 0;
        }

        // Perform bulk update
        $affectedRows = Siswa::whereIn('id', $validStudentIds)
            ->update(['kelas_id' => $toKelasId]);

        Log::info("Promoted students from Kelas {$fromKelasId} to {$toKelasId}", [
            'count' => $affectedRows,
            'student_ids' => $validStudentIds,
        ]);

        return $affectedRows;
    }

    /**
     * Mark Kelas 9 students as graduated
     *
     * Updates:
     * - status_akademik → "Lulus SMP"
     * - tanggal_lulus_akademik → graduation date
     * - kelas_id remains 9 (preserved for historical record)
     *
     * @param int $kelas9Id The Kelas 9 ID
     * @param Carbon $graduationDate The graduation date to record
     * @return int Count of students graduated
     */
    private function graduateStudents(int $kelas9Id, Carbon $graduationDate): int
    {
        // Get eligible students from Kelas 9
        $eligibleStudents = Siswa::eligibleForPromotion($kelas9Id)->get();

        // Validate eligibility for each student
        $validStudentIds = $eligibleStudents->filter(function (Siswa $siswa) {
            return $this->canPromote($siswa);
        })->pluck('id')->toArray();

        if (empty($validStudentIds)) {
            return 0;
        }

        // Perform bulk update to mark as graduated
        $affectedRows = Siswa::whereIn('id', $validStudentIds)
            ->update([
                'status_akademik' => StatusAkademik::LULUS_SMP->value,
                'tanggal_lulus_akademik' => $graduationDate,
            ]);

        Log::info("Graduated students from Kelas {$kelas9Id}", [
            'count' => $affectedRows,
            'graduation_date' => $graduationDate->toDateString(),
            'student_ids' => $validStudentIds,
        ]);

        return $affectedRows;
    }

    /**
     * Validate that student progression is valid
     *
     * Checks:
     * - Student has status_akademik = 'Aktif'
     * - Student has a valid kelas_id
     * - Student's Kelas is within valid range (7-9)
     *
     * @param Siswa $siswa The student to validate
     * @return bool True if student can be promoted
     */
    private function canPromote(Siswa $siswa): bool
    {
        // Must be active
        if ($siswa->status_akademik !== StatusAkademik::AKTIF->value) {
            return false;
        }

        // Must have a valid Kelas assignment
        if (!$siswa->kelas_id || !$siswa->kelas) {
            Log::warning("Student {$siswa->id} has no valid Kelas assignment", [
                'siswa_id' => $siswa->id,
                'kelas_id' => $siswa->kelas_id,
            ]);
            return false;
        }

        // Kelas tingkat must be valid (7, 8, or 9)
        $tingkat = $siswa->kelas->tingkat;
        if (!in_array($tingkat, ['7', '8', '9'])) {
            Log::error("Student {$siswa->id} has invalid Kelas tingkat: {$tingkat}", [
                'siswa_id' => $siswa->id,
                'kelas_id' => $siswa->kelas_id,
                'tingkat' => $tingkat,
            ]);
            return false;
        }

        return true;
    }
}
