<?php

namespace App\Services;

use App\Models\UsulanKenaikanTingkat;
use App\Models\Siswa;
use App\Models\Kelompok;
use App\Models\ProgresHafalan;
use App\Models\ParameterKonfigurasi;
use App\Models\RiwayatKelompok;
use App\Enums\StatusUsulan;
use App\Enums\JenisKelompok;
use App\Exceptions\IneligibleForPromotionException;
use App\Exceptions\InvalidLevelProgressionException;
use App\Exceptions\InvalidStatusTransitionException;
use App\Exceptions\GroupCapacityExceededException;
use App\Services\Contracts\UsulanKenaikanTingkatServiceInterface;
use App\Services\Contracts\NotifikasiServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\Activitylog\Facades\LogActivity;

/**
 * Service for Usulan Kenaikan Tingkat (Level Advancement Proposal) operations
 *
 * Handles the complete workflow of student level promotion including:
 * - Eligibility validation based on memorization progress
 * - Proposal creation, approval, and rejection
 * - Student placement to target groups
 * - Available group filtering with capacity checks
 *
 * Business Rules:
 * - Eligibility: total_juz_tuntas >= Batas_Tingkat[tingkat_tujuan]
 * - Level Progression: Can only propose to higher level (Dasar → Menengah → Atas)
 * - Approval Independence: Admin can approve without selecting target group
 * - Placement Prerequisites: Only "Disetujui" proposals can be placed
 * - Group Capacity: Placement blocked if jumlah_siswa >= kapasitas_maks
 * - Gender Matching: Available groups filtered by jenis_kelamin
 * - Historical Immutability: Once placed, proposal cannot be edited
 */
class UsulanKenaikanTingkatService implements UsulanKenaikanTingkatServiceInterface
{
    /**
     * Constructor
     *
     * @param NotifikasiServiceInterface $notifikasiService Notification dispatch service
     */
    public function __construct(
        protected NotifikasiServiceInterface $notifikasiService
    ) {}
    /**
     * Validate student eligibility for level promotion
     *
     * Checks if student's total_juz_tuntas meets or exceeds the threshold
     * for the target level from Batas_Tingkat configuration.
     *
     * @param int $siswaId Student ID to validate
     * @param string $tingkatTujuan Target level (Dasar/Menengah/Atas)
     * @return bool True if eligible
     * @throws IneligibleForPromotionException if juz < threshold
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException if student not found
     *
     * Validates: Requirements 2.1, 2.2, 2.3, 2.4
     */
    public function validateEligibility(int $siswaId, string $tingkatTujuan): bool
    {
        // Retrieve student's progres hafalan (Requirement 2.1)
        $siswa = Siswa::findOrFail($siswaId);

        // Get the latest progres hafalan for current academic year
        $progresHafalan = ProgresHafalan::where('siswa_id', $siswaId)
            ->latest()
            ->first();

        if (!$progresHafalan) {
            throw new IneligibleForPromotionException(
                "Data progres hafalan tidak ditemukan untuk siswa {$siswa->nama}."
            );
        }

        $totalJuzTuntas = $progresHafalan->total_juz_tuntas;

        // Retrieve Batas_Tingkat configuration (Requirement 2.2)
        $config = ParameterKonfigurasi::getInstance();
        $batasTingkat = $config->batas_tingkat;

        // Map tingkat to array key
        $tingkatKey = match($tingkatTujuan) {
            'Dasar' => 'dasar',
            'Menengah' => 'menengah',
            'Atas' => 'atas',
            default => throw new \InvalidArgumentException("Invalid tingkat: {$tingkatTujuan}")
        };

        $threshold = $batasTingkat[$tingkatKey] ?? 0;

        // Check if student meets threshold (Requirements 2.3, 2.4)
        if ($totalJuzTuntas < $threshold) {
            throw new IneligibleForPromotionException(
                "Siswa {$siswa->nama} belum memenuhi syarat kenaikan tingkat. " .
                "Minimum {$threshold} juz diperlukan untuk {$tingkatTujuan}, " .
                "saat ini {$totalJuzTuntas} juz."
            );
        }

        return true;
    }

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
     * @throws IneligibleForPromotionException if juz < threshold
     * @throws InvalidLevelProgressionException if tingkat_tujuan <= tingkat_asal
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException if student or teacher not found
     *
     * Validates: Requirements 1.2, 1.3, 1.4, 2.3, 2.4, 2.5
     */
    public function createUsulan(
        int $siswaId,
        int $guruPengusulId,
        string $tingkatTujuan,
        string $alasan
    ): UsulanKenaikanTingkat {
        // Call validateEligibility before creating proposal (Requirement 2.3, 2.4)
        $this->validateEligibility($siswaId, $tingkatTujuan);

        // Retrieve student with kelompok
        $siswa = Siswa::with('kelompok')->findOrFail($siswaId);

        if (!$siswa->kelompok) {
            throw new \RuntimeException(
                "Siswa {$siswa->nama} belum ditempatkan di kelompok manapun."
            );
        }

        // Auto-populate tingkat_asal from student's current kelompok (Requirement 1.2)
        $tingkatAsal = $siswa->kelompok->tingkat;
        $kelompokAsalId = $siswa->kelompok->id;

        // Validate level progression is upward only (Requirement 2.5)
        $tingkatOrder = [
            'Dasar' => 1,
            'Menengah' => 2,
            'Atas' => 3,
        ];

        $tingkatAsalValue = $tingkatOrder[$tingkatAsal] ?? 0;
        $tingkatTujuanValue = $tingkatOrder[$tingkatTujuan] ?? 0;

        if ($tingkatTujuanValue <= $tingkatAsalValue) {
            throw new InvalidLevelProgressionException(
                "Kenaikan tingkat harus ke level yang lebih tinggi. " .
                "Tingkat asal: {$tingkatAsal}, tingkat tujuan: {$tingkatTujuan}."
            );
        }

        // Create usulan with status "Diajukan" (Requirements 1.3, 1.4)
        $usulan = new UsulanKenaikanTingkat([
            'siswa_id' => $siswaId,
            'guru_pengusul_id' => $guruPengusulId,
            'tingkat_asal' => $tingkatAsal,
            'tingkat_tujuan' => $tingkatTujuan,
            'kelompok_asal_id' => $kelompokAsalId,
            'status_usulan' => StatusUsulan::DIAJUKAN,
            'alasan' => $alasan,
            'tanggal_usul' => now(),
        ]);

        $usulan->save();

        // Log proposal creation (Requirement 4.2)
        activity()
            ->performedOn($usulan)
            ->withProperties([
                'siswa_nama' => $siswa->nama,
                'tingkat_asal' => $tingkatAsal,
                'tingkat_tujuan' => $tingkatTujuan,
                'status_usulan' => StatusUsulan::DIAJUKAN->value,
                'causer_type' => 'Guru',
                'causer_id' => $guruPengusulId,
            ])
            ->log('Usulan kenaikan tingkat dibuat');

        return $usulan;
    }

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
     * @throws InvalidStatusTransitionException if not in "Diajukan" status
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException if proposal not found
     *
     * Validates: Requirements 4.2, 4.5, 5.1, 5.2
     */
    public function approveUsulan(
        int $usulanId,
        int $adminVerifikatorId,
        ?string $catatanKeputusan = null,
        ?string $tanggalKeputusan = null
    ): UsulanKenaikanTingkat {
        return DB::transaction(function () use ($usulanId, $adminVerifikatorId, $catatanKeputusan, $tanggalKeputusan) {
            $usulan = UsulanKenaikanTingkat::lockForUpdate()->findOrFail($usulanId);

            // Validate current status is "Diajukan"
            if ($usulan->status_usulan !== StatusUsulan::DIAJUKAN) {
                throw new InvalidStatusTransitionException(
                    $usulan->status_usulan->value,
                    'menyetujui',
                    StatusUsulan::DIAJUKAN->value
                );
            }

            // Update to "Disetujui" status
            $usulan->status_usulan        = StatusUsulan::DISETUJUI;
            $usulan->admin_verifikator_id = $adminVerifikatorId;
            $usulan->tanggal_keputusan    = $tanggalKeputusan ? \Carbon\Carbon::parse($tanggalKeputusan) : now();
            $usulan->catatan_keputusan    = $catatanKeputusan;
            $usulan->save();

            // Log approval
            activity()
                ->performedOn($usulan)
                ->withProperties([
                    'siswa_nama'        => $usulan->siswa->nama ?? null,
                    'status_lama'       => StatusUsulan::DIAJUKAN->value,
                    'status_baru'       => StatusUsulan::DISETUJUI->value,
                    'tanggal_keputusan' => $usulan->tanggal_keputusan,
                    'catatan_keputusan' => $catatanKeputusan,
                    'causer_type'       => 'Admin',
                    'causer_id'         => $adminVerifikatorId,
                ])
                ->log('Usulan kenaikan tingkat disetujui');

            // Dispatch notification
            $this->notifikasiService->notifyGuruOnDecision($usulan, 'approved');

            return $usulan;
        });
    }

    /**
     * Reject promotion proposal
     *
     * Transitions proposal from "Diajukan" to "Ditolak" status.
     * Sets admin_verifikator_id and tanggal_keputusan. Requires
     * catatan_keputusan explaining the rejection.
     *
     * @param int $usulanId Proposal ID to reject
     * @param int $adminVerifikatorId Admin performing the rejection
     * @param string $catatanKeputusan Required decision notes
     * @param string|null $tanggalKeputusan Optional custom decision date
     * @return UsulanKenaikanTingkat Updated proposal
     * @throws InvalidStatusTransitionException if not in "Diajukan" status
     * @throws ValidationException if catatan_keputusan is empty
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException if proposal not found
     *
     * Validates: Requirements 4.3, 4.4
     */
    public function rejectUsulan(
        int $usulanId,
        int $adminVerifikatorId,
        string $catatanKeputusan,
        ?string $tanggalKeputusan = null
    ): UsulanKenaikanTingkat {
        // Validate catatan_keputusan is required for rejection
        if (empty(trim($catatanKeputusan))) {
            throw ValidationException::withMessages([
                'catatan_keputusan' => 'Catatan keputusan wajib diisi saat menolak usulan.'
            ]);
        }

        return DB::transaction(function () use ($usulanId, $adminVerifikatorId, $catatanKeputusan, $tanggalKeputusan) {
            $usulan = UsulanKenaikanTingkat::lockForUpdate()->findOrFail($usulanId);

            // Validate current status is "Diajukan"
            if ($usulan->status_usulan !== StatusUsulan::DIAJUKAN) {
                throw new InvalidStatusTransitionException(
                    $usulan->status_usulan->value,
                    'menolak',
                    StatusUsulan::DIAJUKAN->value
                );
            }

            // Update to "Ditolak" status
            $usulan->status_usulan        = StatusUsulan::DITOLAK;
            $usulan->admin_verifikator_id = $adminVerifikatorId;
            $usulan->tanggal_keputusan    = $tanggalKeputusan ? \Carbon\Carbon::parse($tanggalKeputusan) : now();
            $usulan->catatan_keputusan    = $catatanKeputusan;
            $usulan->save();

            // Log rejection
            activity()
                ->performedOn($usulan)
                ->withProperties([
                    'siswa_nama'        => $usulan->siswa->nama ?? null,
                    'status_lama'       => StatusUsulan::DIAJUKAN->value,
                    'status_baru'       => StatusUsulan::DITOLAK->value,
                    'tanggal_keputusan' => $usulan->tanggal_keputusan,
                    'catatan_keputusan' => $catatanKeputusan,
                    'causer_type'       => 'Admin',
                    'causer_id'         => $adminVerifikatorId,
                ])
                ->log('Usulan kenaikan tingkat ditolak');

            // Dispatch notification
            $this->notifikasiService->notifyGuruOnDecision($usulan, 'rejected');

            return $usulan;
        });
    }

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
     * @throws InvalidStatusTransitionException if not in "Disetujui" status
     * @throws ValidationException if alasan_pembatalan is invalid
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException if proposal not found
     */
    public function cancelApproval(
        int $usulanId,
        int $adminVerifikatorId,
        string $alasanPembatalan
    ): UsulanKenaikanTingkat {
        // Validate alasan_pembatalan is required
        if (empty(trim($alasanPembatalan)) || strlen(trim($alasanPembatalan)) < 10) {
            throw ValidationException::withMessages([
                'alasan_pembatalan' => 'Alasan pembatalan wajib diisi minimal 10 karakter.'
            ]);
        }

        $usulan = UsulanKenaikanTingkat::findOrFail($usulanId);

        // Validate current status is "Disetujui" (not yet placed)
        if ($usulan->status_usulan !== StatusUsulan::DISETUJUI) {
            throw new InvalidStatusTransitionException(
                $usulan->status_usulan->value,
                'membatalkan',
                StatusUsulan::DISETUJUI->value
            );
        }

        // Rollback to "Diajukan" status
        $oldAdminId = $usulan->admin_verifikator_id;
        $oldTanggalKeputusan = $usulan->tanggal_keputusan;
        $oldCatatan = $usulan->catatan_keputusan;

        $usulan->status_usulan = StatusUsulan::DIAJUKAN;
        $usulan->admin_verifikator_id = null;
        $usulan->tanggal_keputusan = null;
        $usulan->catatan_keputusan = null;

        $usulan->save();

        // Log cancellation
        activity()
            ->performedOn($usulan)
            ->withProperties([
                'siswa_nama' => $usulan->siswa->nama ?? null,
                'status_lama' => StatusUsulan::DISETUJUI->value,
                'status_baru' => StatusUsulan::DIAJUKAN->value,
                'alasan_pembatalan' => $alasanPembatalan,
                'old_admin_verifikator_id' => $oldAdminId,
                'old_tanggal_keputusan' => $oldTanggalKeputusan,
                'old_catatan_keputusan' => $oldCatatan,
                'causer_type' => 'Admin',
                'causer_id' => $adminVerifikatorId,
            ])
            ->log('Persetujuan usulan kenaikan tingkat dibatalkan');

        // Dispatch notification to guru_pengusul via NotifikasiService
        $this->notifikasiService->notifyGuruOnDecision($usulan, 'cancelled');

        return $usulan;
    }

    /**
     * Cancel a placed proposal (rollback placement)
     *
     * Reverts proposal from "Ditempatkan" back to "Disetujui" status.
     * This allows admin to undo placement when there was an error.
     *
     * ROLLBACK ACTIONS:
     * - Move student back to original kelompok (siswa.kelompok_id)
     * - Delete the placement riwayat_kelompok entry
     * - Reset usulan status to "Disetujui"
     * - Clear kelompok_tujuan_id and tanggal_penempatan
     *
     * All updates performed in atomic transaction.
     *
     * @param int $usulanId Proposal ID to cancel placement
     * @param int $adminVerifikatorId Admin ID who cancels the placement
     * @param string $alasanPembatalan Reason for cancellation (required, min 10 chars)
     * @return UsulanKenaikanTingkat Updated proposal
     * @throws InvalidStatusTransitionException if not in "Ditempatkan" status
     * @throws ValidationException if alasan_pembatalan is invalid
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException if proposal not found
     */
    public function cancelPlacement(
        int $usulanId,
        int $adminVerifikatorId,
        string $alasanPembatalan
    ): UsulanKenaikanTingkat {
        // Validate alasan_pembatalan is required
        if (empty(trim($alasanPembatalan)) || strlen(trim($alasanPembatalan)) < 10) {
            throw ValidationException::withMessages([
                'alasan_pembatalan' => 'Alasan pembatalan wajib diisi minimal 10 karakter.'
            ]);
        }

        return DB::transaction(function () use ($usulanId, $adminVerifikatorId, $alasanPembatalan) {
            // Lock usulan record for update
            $usulan = UsulanKenaikanTingkat::with('siswa')->lockForUpdate()->findOrFail($usulanId);

            // Validate current status is "Ditempatkan"
            if ($usulan->status_usulan !== StatusUsulan::DITEMPATKAN) {
                throw new InvalidStatusTransitionException(
                    $usulan->status_usulan->value,
                    'membatalkan penempatan',
                    StatusUsulan::DITEMPATKAN->value
                );
            }

            $siswa = $usulan->siswa;

            // Store old values for logging
            $oldKelompokTujuanId = $usulan->kelompok_tujuan_id;
            $oldTanggalPenempatan = $usulan->tanggal_penempatan;
            $kelompokAsalId = $usulan->kelompok_asal_id;

            // ROLLBACK ACTION 1: Move student back to original kelompok
            $siswa->kelompok_id = $kelompokAsalId;
            $siswa->save();

            // ROLLBACK ACTION 2: Delete the placement riwayat_kelompok entry
            // Find and delete the specific riwayat entry for this placement
            RiwayatKelompok::where('siswa_id', $siswa->id)
                ->where('kelompok_dari_id', $kelompokAsalId)
                ->where('kelompok_ke_id', $oldKelompokTujuanId)
                ->where('alasan', 'Kenaikan Tingkat')
                ->where('disetujui_oleh', $usulan->admin_verifikator_id)
                ->delete();

            // ROLLBACK ACTION 3: Reset usulan directly to "Diajukan" status
            $oldAdminId = $usulan->admin_verifikator_id;
            $oldTanggalKeputusan = $usulan->tanggal_keputusan;
            $oldCatatan = $usulan->catatan_keputusan;

            $usulan->status_usulan = StatusUsulan::DIAJUKAN;
            $usulan->kelompok_tujuan_id = null;
            $usulan->tanggal_penempatan = null;
            $usulan->admin_verifikator_id = null;
            $usulan->tanggal_keputusan = null;
            $usulan->catatan_keputusan = null;
            $usulan->save();

            // Log placement and approval cancellation
            activity()
                ->performedOn($usulan)
                ->withProperties([
                    'siswa_nama' => $siswa->nama,
                    'siswa_nis' => $siswa->nis,
                    'status_lama' => StatusUsulan::DITEMPATKAN->value,
                    'status_baru' => StatusUsulan::DIAJUKAN->value,
                    'alasan_pembatalan' => $alasanPembatalan,
                    'old_kelompok_tujuan_id' => $oldKelompokTujuanId,
                    'old_tanggal_penempatan' => $oldTanggalPenempatan,
                    'rollback_to_kelompok_id' => $kelompokAsalId,
                    'old_admin_verifikator_id' => $oldAdminId,
                    'old_tanggal_keputusan' => $oldTanggalKeputusan,
                    'old_catatan_keputusan' => $oldCatatan,
                    'causer_type' => 'Admin',
                    'causer_id' => $adminVerifikatorId,
                ])
                ->log('Penempatan dan persetujuan usulan kenaikan tingkat dibatalkan (rollback ke kelompok asal & status diajukan)');

            // Dispatch notification to siswa and guru
            $this->notifikasiService->notifyGuruOnDecision($usulan, 'cancelled');
            $this->notifikasiService->notifySiswaOnPlacementCancelled($usulan);

            return $usulan->fresh();
        });
    }

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
     * @param string|null $tanggalPenempatan Optional custom placement date (Y-m-d format), defaults to today
     * @return UsulanKenaikanTingkat Updated proposal
     * @throws GroupCapacityExceededException if group is full
     * @throws InvalidStatusTransitionException if not in "Disetujui" status
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException if proposal or group not found
     *
     * Validates: Requirements 6.4, 6.5, 6.6, 7.1, 7.2, 7.3, 7.4
     */
    public function placeStudent(
        int $usulanId,
        int $kelompokTujuanId,
        ?string $tanggalPenempatan = null
    ): UsulanKenaikanTingkat {
        return DB::transaction(function () use ($usulanId, $kelompokTujuanId, $tanggalPenempatan) {
            // Lock usulan record for update
            $usulan = UsulanKenaikanTingkat::with('siswa')->lockForUpdate()->findOrFail($usulanId);

            // Validate current status is "Disetujui"
            if ($usulan->status_usulan !== StatusUsulan::DISETUJUI) {
                throw new InvalidStatusTransitionException(
                    $usulan->status_usulan->value,
                    'menempatkan',
                    StatusUsulan::DISETUJUI->value
                );
            }

            // Lock kelompok record and check capacity
            $kelompokTujuan = Kelompok::lockForUpdate()->findOrFail($kelompokTujuanId);

            // Re-check capacity within transaction (concurrency safety)
            $currentJumlahSiswa = $kelompokTujuan->siswas()->count();

            if ($currentJumlahSiswa >= $kelompokTujuan->kapasitas_maks) {
                throw new GroupCapacityExceededException(
                    $kelompokTujuan->nama,
                    $currentJumlahSiswa,
                    $kelompokTujuan->kapasitas_maks
                );
            }

            $siswa = $usulan->siswa;
            $kelompokDariId = $siswa->kelompok_id;

            // Parse tanggal_penempatan or use today's date
            $tanggalPenempatanCarbon = $tanggalPenempatan
                ? \Carbon\Carbon::parse($tanggalPenempatan)
                : now();

            // Update usulan to "Ditempatkan" (Requirements 6.4, 6.5, 6.6)
            $usulan->kelompok_tujuan_id = $kelompokTujuanId;
            $usulan->status_usulan = StatusUsulan::DITEMPATKAN;
            $usulan->tanggal_penempatan = $tanggalPenempatanCarbon;
            $usulan->save();

            // Update siswa's kelompok_id and status_penempatan
            $siswa->kelompok_id = $kelompokTujuanId;
            $siswa->status_penempatan = 'Ditempatkan';
            $siswa->tanggal_penempatan = $tanggalPenempatanCarbon; // ✅ Record placement date
            $siswa->save();

            // Create Riwayat_Kelompok entry (Requirements 7.1, 7.2, 7.3, 7.4)
            // Use custom tanggal_penempatan as the historical placement date
            // If custom date provided by admin, use it directly for riwayat
            RiwayatKelompok::create([
                'siswa_id' => $siswa->id,
                'kelompok_dari_id' => $kelompokDariId,
                'kelompok_ke_id' => $kelompokTujuanId,
                'tanggal' => $tanggalPenempatanCarbon,
                'alasan' => 'Kenaikan Tingkat',
                'disetujui_oleh' => $usulan->admin_verifikator_id,
            ]);

            // Dispatch notification to siswa via NotifikasiService (Requirement 9.1)
            $this->notifikasiService->notifySiswaOnPlacement($usulan);

            // Log placement (Requirement 6.4)
            activity()
                ->performedOn($usulan)
                ->withProperties([
                    'siswa_nama' => $siswa->nama,
                    'status_lama' => StatusUsulan::DISETUJUI->value,
                    'status_baru' => StatusUsulan::DITEMPATKAN->value,
                    'kelompok_dari' => $usulan->kelompokAsal->nama ?? null,
                    'kelompok_ke' => $kelompokTujuan->nama,
                    'tanggal_penempatan' => $usulan->tanggal_penempatan,
                    'causer_type' => 'Admin',
                    'causer_id' => $usulan->admin_verifikator_id,
                ])
                ->log('Siswa ditempatkan ke kelompok baru');

            return $usulan->fresh();
        });
    }

    /**
     * Get available groups for placement
     *
     * Returns groups matching:
     * - tingkat = tingkat_tujuan (Requirement 6.1)
     * - jenis matches student's jenis_kelamin (Banin/Banat) (Requirement 6.2)
     * - jumlah_siswa < kapasitas_maks (Requirement 6.3)
     *
     * Returns collection with: name, guru_pembimbing, jumlah_siswa, kapasitas_maks
     *
     * @param string $tingkatTujuan Target level to filter groups
     * @param string $jenisKelamin Student's gender for matching (Laki-laki/Perempuan)
     * @return Collection<Kelompok> Available groups with capacity
     *
     * Validates: Requirements 6.1, 6.2, 6.3, 12.3
     */
    public function getAvailableGroups(
        string $tingkatTujuan,
        string $jenisKelamin
    ): Collection {
        // Map jenis_kelamin to JenisKelompok enum (Requirement 6.2)
        $jenisKelompok = match($jenisKelamin) {
            'Laki-laki' => JenisKelompok::BANIN->value,
            'Perempuan' => JenisKelompok::BANAT->value,
            default => throw new \InvalidArgumentException("Invalid jenis_kelamin: {$jenisKelamin}")
        };

        // Get all kelompok matching tingkat and jenis (Requirements 6.1, 6.2)
        $kelompoks = Kelompok::with(['guruPembimbing', 'siswas'])
            ->where('tingkat', $tingkatTujuan)
            ->where('jenis', $jenisKelompok)
            ->get();

        // Filter out full groups (Requirement 6.3)
        // Exclude groups where jumlah_siswa >= kapasitas_maks
        $availableGroups = $kelompoks->filter(function ($kelompok) {
            $jumlahSiswa = $kelompok->siswas->count();
            return $jumlahSiswa < $kelompok->kapasitas_maks;
        });

        // Return collection with reindexed keys
        return $availableGroups->values();
    }
}
