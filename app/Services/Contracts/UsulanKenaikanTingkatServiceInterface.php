<?php

namespace App\Services\Contracts;

use App\Models\UsulanKenaikanTingkat;
use Illuminate\Support\Collection;

/**
 * Service interface for Usulan Kenaikan Tingkat operations
 *
 * Handles the complete workflow of student level promotion including:
 * - Eligibility validation based on memorization progress
 * - Proposal creation, approval, and rejection
 * - Student placement to target groups
 * - Available group filtering with capacity checks
 */
interface UsulanKenaikanTingkatServiceInterface
{
    /**
     * Validate student eligibility for level promotion
     *
     * Checks if student's total_juz_tuntas meets or exceeds the threshold
     * for the target level (Batas_Tingkat configuration).
     *
     * @param int $siswaId Student ID to validate
     * @param string $tingkatTujuan Target level (Dasar/Menengah/Atas)
     * @return bool True if eligible
     * @throws \App\Exceptions\IneligibleForPromotionException if juz < threshold
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException if student not found
     *
     * Validates: Requirements 2.1, 2.2, 2.3, 2.4
     */
    public function validateEligibility(int $siswaId, string $tingkatTujuan): bool;

    /**
     * Create new promotion proposal
     *
     * Creates a new usulan record with status "Diajukan". Automatically
     * populates tingkat_asal from student's current kelompok and sets
     * tanggal_usul to current timestamp.
     *
     * @param int $siswaId Student being proposed for promotion
     * @param int $guruPengusulId Teacher submitting the proposal
     * @param string $tingkatTujuan Target level (must be higher than current)
     * @param string $alasan Teacher's justification for promotion
     * @return UsulanKenaikanTingkat Created proposal
     * @throws \App\Exceptions\InvalidLevelProgressionException if tingkat_tujuan <= tingkat_asal
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException if student or teacher not found
     *
     * Validates: Requirements 1.2, 1.3, 1.4
     */
    public function createUsulan(
        int $siswaId,
        int $guruPengusulId,
        string $tingkatTujuan,
        string $alasan
    ): UsulanKenaikanTingkat;

    /**
     * Approve promotion proposal
     *
     * Transitions proposal from "Diajukan" to "Disetujui" status.
     * Sets admin_verifikator_id and tanggal_keputusan. Does NOT assign
     * kelompok_tujuan_id (remains NULL until placement).
     *
     * @param int $usulanId Proposal ID to approve
     * @param int $adminVerifikatorId Admin performing the approval
     * @param string|null $catatanKeputusan Optional decision notes
     * @return UsulanKenaikanTingkat Updated proposal
     * @throws \App\Exceptions\InvalidStatusTransitionException if not in "Diajukan" status
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException if proposal not found
     *
     * Validates: Requirements 4.2, 4.5, 5.1, 5.2
     */
    public function approveUsulan(
        int $usulanId,
        int $adminVerifikatorId,
        ?string $catatanKeputusan = null,
        ?string $tanggalKeputusan = null
    ): UsulanKenaikanTingkat;

    /**
     * Reject promotion proposal
     *
     * Transitions proposal from "Diajukan" to "Ditolak" status.
     * Sets admin_verifikator_id, tanggal_keputusan, and requires catatan_keputusan.
     * Triggers notification to proposing teacher.
     *
     * @param int $usulanId Proposal ID to reject
     * @param int $adminVerifikatorId Admin performing the rejection
     * @param string $catatanKeputusan Required decision notes
     * @param string|null $tanggalKeputusan Optional custom decision date
     * @return UsulanKenaikanTingkat Updated proposal
     * @throws \App\Exceptions\InvalidStatusTransitionException if not in "Diajukan" status
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException if proposal not found
     *
     * Validates: Requirements 4.3, 4.4, 5.1, 5.3
     */
    public function rejectUsulan(
        int $usulanId,
        int $adminVerifikatorId,
        string $catatanKeputusan,
        ?string $tanggalKeputusan = null
    ): UsulanKenaikanTingkat;

    /**
     * Cancel an approved proposal (rollback approval)
     *
     * Reverts proposal from "Disetujui" back to "Diajukan" status.
     * This allows admin to undo approval decisions before student placement.
     *
     * @param int $usulanId Proposal ID to cancel
     * @param int $adminVerifikatorId Admin ID who cancels the approval
     * @param string $alasanPembatalan Reason for cancellation (required, min 10 chars)
     * @return UsulanKenaikanTingkat Updated proposal
     * @throws \App\Exceptions\InvalidStatusTransitionException if not in "Disetujui" status
     * @throws \Illuminate\Validation\ValidationException if alasan_pembatalan is invalid
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException if proposal not found
     */
    public function cancelApproval(
        int $usulanId,
        int $adminVerifikatorId,
        string $alasanPembatalan
    ): UsulanKenaikanTingkat;

    /**
     * Cancel a placed proposal (rollback placement and approval)
     *
     * Reverts proposal from "Ditempatkan" directly back to "Diajukan" status in one step.
     *
     * ROLLBACK ACTIONS:
     * - Move student back to original kelompok
     * - Delete the placement riwayat_kelompok entry
     * - Clear decision and placement data (admin_verifikator_id, tanggal_keputusan, catatan_keputusan, kelompok_tujuan_id, tanggal_penempatan)
     * - Reset usulan status to "Diajukan"
     *
     * All updates performed in atomic transaction.
     *
     * @param int $usulanId Proposal ID to cancel placement
     * @param int $adminVerifikatorId Admin ID who cancels the placement
     * @param string $alasanPembatalan Reason for cancellation (required, min 10 chars)
     * @return UsulanKenaikanTingkat Updated proposal
     * @throws \App\Exceptions\InvalidStatusTransitionException if not in "Ditempatkan" status
     * @throws \Illuminate\Validation\ValidationException if alasan_pembatalan is invalid
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException if proposal not found
     */
    public function cancelPlacement(
        int $usulanId,
        int $adminVerifikatorId,
        string $alasanPembatalan
    ): UsulanKenaikanTingkat;

    /**
     * Place student to target group
     *
     * Transitions proposal from "Disetujui" to "Ditempatkan". Updates:
     * - usulan.kelompok_tujuan_id
     * - usulan.status_usulan to "Ditempatkan"
     * - usulan.tanggal_penempatan
     * - siswa.kelompok_id
     * - Creates Riwayat_Kelompok entry
     *
     * All updates performed in atomic transaction.
     *
     * @param int $usulanId Proposal ID to place
     * @param int $kelompokTujuanId Target group ID
     * @param string|null $tanggalPenempatan Custom placement date (Y-m-d format), defaults to today
     * @return UsulanKenaikanTingkat Updated proposal
     * @throws \App\Exceptions\GroupCapacityExceededException if group is full
     * @throws \App\Exceptions\InvalidStatusTransitionException if not in "Disetujui" status
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException if proposal or group not found
     *
     * Validates: Requirements 6.4, 6.5, 6.6, 7.1, 7.2, 7.3, 7.4
     */
    public function placeStudent(
        int $usulanId,
        int $kelompokTujuanId,
        ?string $tanggalPenempatan = null
    ): UsulanKenaikanTingkat;

    /**
     * Get available groups for placement
     *
     * Returns groups matching:
     * - tingkat = tingkat_tujuan
     * - jenis matches student's jenis_kelamin (Banin/Banat)
     * - jumlah_siswa < kapasitas_maks
     *
     * @param string $tingkatTujuan Target level to filter groups
     * @param string $jenisKelamin Student's gender for matching (Laki-laki/Perempuan)
     * @return Collection<Kelompok> Available groups with capacity
     *
     * Validates: Requirements 6.1, 6.2, 6.3
     */
    public function getAvailableGroups(
        string $tingkatTujuan,
        string $jenisKelamin
    ): Collection;
}
