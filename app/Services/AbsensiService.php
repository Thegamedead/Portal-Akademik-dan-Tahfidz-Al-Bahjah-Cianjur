<?php

namespace App\Services;

use App\Models\Absensi;
use App\Enums\StatusKehadiran;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AbsensiService
{
    /**
     * Save/update absensi for multiple students in a kelompok
     *
     * Req 1, 14, 26: Upsert behavior, alfa counter update, prevent duplicates
     *
     * @param int $kelompokId
     * @param Carbon $tanggal
     * @param array $absensisData Format: [siswa_id => status_kehadiran_string]
     * @param int $guruPengisiId
     * @param bool $isAdmin Whether this save is performed by Admin
     * @param int|null $adminId Admin ID if override is applicable
     * @return void
     */
    public function saveAbsensiKelompok(
        int $kelompokId,
        Carbon $tanggal,
        array $absensisData,
        int $guruPengisiId,
        bool $isAdmin = false,
        ?int $adminId = null,
        ?string $waktuMulai = null,
        ?string $waktuSelesai = null
    ): void {
        DB::transaction(function () use ($kelompokId, $tanggal, $absensisData, $guruPengisiId, $isAdmin, $adminId, $waktuMulai, $waktuSelesai) {
            foreach ($absensisData as $siswaId => $statusString) {
                // Convert string to enum
                $status = StatusKehadiran::from($statusString);

                // Upsert absensi record (Req 26: prevent duplicate with unique constraint)
                $absensi = Absensi::updateOrCreate(
                    [
                        'kelompok_id' => $kelompokId,
                        'siswa_id' => $siswaId,
                        'tanggal' => $tanggal,
                    ],
                    [
                        'status_kehadiran' => $status,
                        'guru_pengisi_id' => $guruPengisiId,
                        'waktu_mulai' => $waktuMulai,
                        'waktu_selesai' => $waktuSelesai,
                    ]
                );

                // Update alfa counter based on status (Req 14)
                $absensi->updateAlfaCounter();

                // Mark admin override if applicable (Req 13)
                if ($isAdmin) {
                    $absensi->markAsOverride($adminId);
                }

                // Save the absensi record
                $absensi->save();

                // Trigger event if alfa threshold reached (Req 14.2)
                // Module 9 will handle notification logic
                // TODO: Implement AlfaThresholdReached event in Module 9
                if ($absensi->hasReachedAlfaThreshold()) {
                    // Event will be implemented in Module 9 (Notifikasi)
                    // event(new \App\Events\AlfaThresholdReached($absensi));
                }
            }
        });
    }

    /**
     * Get summary count per status for confirmation dialog
     *
     * Req 17: Show count per status before submit
     *
     * @param array $absensisData Format: [siswa_id => status_kehadiran_string]
     * @return array Format: ['Hadir' => count, 'Izin' => count, 'Sakit' => count, 'Alfa' => count]
     */
    public function getSummary(array $absensisData): array
    {
        $summary = [
            'Hadir' => 0,
            'Izin' => 0,
            'Sakit' => 0,
            'Alfa' => 0,
            'Libur' => 0,
        ];

        foreach ($absensisData as $status) {
            if (isset($summary[$status])) {
                $summary[$status]++;
            }
        }

        return $summary;
    }

    /**
     * Validate that all students have been marked with a status
     *
     * Req 29: All students must have status selected before submit
     *
     * @param array $absensisData Format: [siswa_id => status_kehadiran_string]
     * @param int $totalSiswaCount Total number of students in kelompok
     * @return bool True if all students marked, false otherwise
     */
    public function validateAllStudentsMarked(array $absensisData, int $totalSiswaCount): bool
    {
        // Check if count matches
        if (count($absensisData) !== $totalSiswaCount) {
            return false;
        }

        // Check if all values are not empty
        foreach ($absensisData as $status) {
            if (empty($status)) {
                return false;
            }
        }

        return true;
    }
}
