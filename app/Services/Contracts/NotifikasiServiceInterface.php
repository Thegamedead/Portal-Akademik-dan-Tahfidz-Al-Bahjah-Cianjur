<?php

namespace App\Services\Contracts;

use App\Models\UsulanKenaikanTingkat;

/**
 * Interface for notification dispatch service
 *
 * Handles notification dispatch for usulan kenaikan tingkat workflow.
 * Notifications are queued to avoid blocking main transactions.
 */
interface NotifikasiServiceInterface
{
    /**
     * Notify Guru when Admin approves or rejects their usulan
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
    ): void;

    /**
     * Notify Siswa when they are placed to a new group after approval
     *
     * @param UsulanKenaikanTingkat $usulan The placement usulan
     * @return void
     *
     * Validates: Requirements 9.1, 9.2
     */
    public function notifySiswaOnPlacement(UsulanKenaikanTingkat $usulan): void;

    /**
     * Notify Siswa when their placement is cancelled (rollback)
     *
     * @param UsulanKenaikanTingkat $usulan The placement usulan that was cancelled
     * @return void
     */
    public function notifySiswaOnPlacementCancelled(UsulanKenaikanTingkat $usulan): void;
}
