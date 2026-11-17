<?php

namespace App\Services\Handlers;

use App\Models\Siswa;
use App\Models\RiwayatKelompok;
use App\Enums\StatusAkademik;
use App\Enums\StatusPenempatan;
use App\DataTransferObjects\TahfidzClearResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Tahfidz Group Handler
 *
 * Handles Tahfidz Kelompok clearing operations during year-end rollover.
 * Processes:
 * - Clearing kelompok_id for all active students
 * - Updating status_penempatan to "Belum Ditempatkan"
 * - Creating RiwayatKelompok audit trail records
 */
class TahfidzGroupHandler
{
    /**
     * Clear Kelompok assignments for all active students
     *
     * Creates RiwayatKelompok records for audit trail.
     * Only processes students with:
     * - status_akademik = 'Aktif'
     * - kelompok_id is not null
     *
     * @param int $adminId The admin performing the operation
     * @return TahfidzClearResult Contains counts and affected student IDs
     */
    public function clearGroups(int $adminId): TahfidzClearResult
    {
        $timestamp = Carbon::now();

        // Get all students eligible for Kelompok clearing
        $students = $this->getStudentsForClearing();

        if ($students->isEmpty()) {
            Log::info('No students found with Kelompok assignments to clear');
            return new TahfidzClearResult(
                clearedCount: 0,
                clearedStudentIds: [],
                riwayatRecordsCreated: 0,
            );
        }

        // Create RiwayatKelompok records before clearing (to preserve original kelompok_id)
        $this->createRiwayatRecords($students, $adminId, $timestamp);

        // Store student IDs for result
        $clearedStudentIds = $students->pluck('id')->toArray();

        // Perform bulk update to clear Kelompok assignments
        $affectedRows = Siswa::whereIn('id', $clearedStudentIds)
            ->update([
                'kelompok_id' => null,
                'status_penempatan' => StatusPenempatan::TERDAFTAR->value,
                'tanggal_penempatan' => null,
            ]);

        Log::info('Tahfidz group clearing completed', [
            'cleared_count' => $affectedRows,
            'riwayat_created' => $students->count(),
            'admin_id' => $adminId,
        ]);

        return new TahfidzClearResult(
            clearedCount: $affectedRows,
            clearedStudentIds: $clearedStudentIds,
            riwayatRecordsCreated: $students->count(),
        );
    }

    /**
     * Determine which students should have groups cleared
     *
     * Returns active students with Kelompok assignments that need to be cleared.
     * Excludes graduated students (status_akademik = "Lulus SMP") as per requirement 4.5.
     *
     * @return Collection Active students with Kelompok assignments
     */
    private function getStudentsForClearing(): Collection
    {
        return Siswa::where('status_akademik', StatusAkademik::AKTIF->value)
            ->whereNotNull('kelompok_id')
            ->with('kelompok') // Eager load for logging purposes
            ->get();
    }

    /**
     * Bulk create RiwayatKelompok records
     *
     * Creates audit trail entries for all students whose Kelompok assignments are being cleared.
     * Each record captures:
     * - Original kelompok_id (kelompok_dari_id)
     * - Null destination (kelompok_ke_id = null, indicating removal)
     * - Reason: "Rollover Tahun Ajaran"
     * - Admin who authorized the operation
     * - Timestamp of the operation
     *
     * Uses chunk processing for performance with large datasets.
     *
     * @param Collection $students Students whose groups were cleared
     * @param int $adminId The admin performing the operation
     * @param Carbon $timestamp The operation timestamp
     * @return void
     */
    private function createRiwayatRecords(
        Collection $students,
        int $adminId,
        Carbon $timestamp
    ): void {
        // Prepare bulk insert data
        $riwayatData = [];

        foreach ($students as $siswa) {
            $riwayatData[] = [
                'siswa_id' => $siswa->id,
                'kelompok_dari_id' => $siswa->kelompok_id,
                'kelompok_ke_id' => null, // Cleared, not moved to another group
                'tanggal' => $timestamp,
                'alasan' => 'Rollover Tahun Ajaran',
                'disetujui_oleh' => $adminId,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        // Bulk insert RiwayatKelompok records in chunks for performance
        $chunks = array_chunk($riwayatData, 500);
        foreach ($chunks as $chunk) {
            RiwayatKelompok::insert($chunk);
        }

        Log::info('Created RiwayatKelompok records for rollover', [
            'count' => count($riwayatData),
            'admin_id' => $adminId,
        ]);
    }
}
