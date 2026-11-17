<?php

namespace App\Services;

use App\Models\UsulanKenaikanTingkat;
use App\Notifications\KenaikanTingkatPlacementNotification;
use App\Notifications\UsulanKenaikanTingkatDecisionNotification;
use App\Services\Contracts\NotifikasiServiceInterface;

/**
 * Service for notification dispatch in usulan kenaikan tingkat workflow
 *
 * This service handles all notification dispatch for the level advancement proposal
 * workflow, including:
 * - Notifying teachers when their proposals are approved/rejected
 * - Notifying students when they are placed to new groups
 *
 * All notifications use Laravel's notification system with the database channel
 * and are queued to avoid blocking main transactions.
 *
 * Validates: Requirements 8.1, 8.2, 9.1, 9.2
 */
class NotifikasiService implements NotifikasiServiceInterface
{
    /**
     * Notify Guru when Admin approves or rejects their usulan
     *
     * Dispatches an in-app notification (database channel) to the Guru_Pengusul
     * containing:
     * - Student name
     * - Decision (approved/rejected)
     * - catatan_keputusan if provided
     * - Decision date
     * - Target level
     *
     * Notification is queued to avoid blocking the approval/rejection transaction.
     *
     * @param UsulanKenaikanTingkat $usulan The proposal that was approved/rejected
     * @param string $decisionType 'approved' or 'rejected'
     * @return void
     *
     * Validates: Requirements 8.1, 8.2
     */
    public function notifyGuruOnDecision(
        UsulanKenaikanTingkat $usulan,
        string $decisionType
    ): void {
        // Validate decision_type parameter
        if (!in_array($decisionType, ['approved', 'rejected', 'placement_cancelled', 'cancelled'])) {
            throw new \InvalidArgumentException(
                "Invalid decision_type: {$decisionType}. Must be 'approved', 'rejected', 'placement_cancelled', or 'cancelled'."
            );
        }

        // Ensure relationships are loaded
        if (!$usulan->relationLoaded('guruPengusul')) {
            $usulan->load('guruPengusul');
        }

        if (!$usulan->relationLoaded('siswa')) {
            $usulan->load('siswa');
        }

        // Dispatch notification to guru_pengusul (Requirement 8.1)
        // Notification content validates Requirement 8.2
        $usulan->guruPengusul->notify(
            new UsulanKenaikanTingkatDecisionNotification($usulan, $decisionType)
        );
    }

    /**
     * Notify Siswa when they are placed to a new group after approval
     *
     * Dispatches an in-app notification (database channel) to the Siswa containing:
     * - New Tingkat_Kelompok
     * - New Kelompok name
     * - Guru pembimbing name
     * - Effective date (tanggal_penempatan)
     *
     * Notification is queued to avoid blocking the placement transaction.
     *
     * @param UsulanKenaikanTingkat $usulan The placement usulan
     * @return void
     *
     * Validates: Requirements 9.1, 9.2
     */
    public function notifySiswaOnPlacement(UsulanKenaikanTingkat $usulan): void
    {
        // Validate that usulan has been placed
        if ($usulan->status_usulan->value !== 'Ditempatkan') {
            throw new \InvalidArgumentException(
                "Cannot notify student on placement: usulan status is {$usulan->status_usulan->value}, expected 'Ditempatkan'."
            );
        }

        // Ensure relationships are loaded
        if (!$usulan->relationLoaded('siswa')) {
            $usulan->load('siswa');
        }

        if (!$usulan->relationLoaded('kelompokTujuan')) {
            $usulan->load(['kelompokTujuan.guruPembimbing']);
        }

        // Dispatch notification to siswa (Requirement 9.1)
        // Notification content validates Requirement 9.2
        $usulan->siswa->notify(
            new KenaikanTingkatPlacementNotification($usulan)
        );
    }

    /**
     * Notify Siswa when their placement is cancelled (rollback)
     *
     * Dispatches an in-app notification to inform the student that their
     * placement has been cancelled and they have been returned to their
     * original kelompok.
     *
     * @param UsulanKenaikanTingkat $usulan The placement usulan that was cancelled
     * @return void
     */
    public function notifySiswaOnPlacementCancelled(UsulanKenaikanTingkat $usulan): void
    {
        // Ensure relationships are loaded
        if (!$usulan->relationLoaded('siswa')) {
            $usulan->load('siswa');
        }

        if (!$usulan->relationLoaded('kelompokAsal')) {
            $usulan->load('kelompokAsal');
        }

        // Dispatch notification to siswa
        $usulan->siswa->notify(
            new \App\Notifications\PlacementCancelledNotification($usulan)
        );
    }

    /**
     * Notify Guru when appointed to a placement test
     */
    public function notifyGuruOnAppointed(\App\Models\TesPenempatan $tes): void
    {
        if (!$tes->relationLoaded('guruPenguji')) {
            $tes->load('guruPenguji');
        }
        if (!$tes->relationLoaded('siswa')) {
            $tes->load('siswa');
        }
        $tes->guruPenguji->notify(new \App\Notifications\GuruAppointedNotification($tes));
    }

    /**
     * Notify about test cancellation
     */
    public function notifyOnTestCancelled(\App\Models\TesPenempatan $tes): void
    {
        if (!$tes->relationLoaded('guruPenguji')) {
            $tes->load('guruPenguji');
        }
        if (!$tes->relationLoaded('siswa')) {
            $tes->load('siswa');
        }
        $tes->guruPenguji->notify(new \App\Notifications\TestCancelledNotification($tes));
        // You could also notify the student here if students have user accounts.
    }

    /**
     * Notify about test results submitted
     */
    public function notifyOnTestResultsSubmitted(\App\Models\TesPenempatan $tes): void
    {
        // Typically notify Admin. Since we might not have a specific admin to notify,
        // we can notify the Student or just skip dispatching if no target exists.
    }

    /**
     * Notify Student when placed after test
     */
    public function notifyStudentOnPlaced(\App\Models\TesPenempatan $tes): void
    {
        if (!$tes->relationLoaded('siswa')) {
            $tes->load('siswa');
        }
        $tes->siswa->notify(new \App\Notifications\StudentPlacedNotification($tes));
    }
}
