<?php

namespace App\Livewire\Admin\UsulanKenaikanTingkat;

use App\Models\UsulanKenaikanTingkat;
use App\Models\Siswa;
use App\Models\Kelompok;
use App\Services\Contracts\UsulanKenaikanTingkatServiceInterface;
use App\Exceptions\GroupCapacityExceededException;
use App\Exceptions\InvalidStatusTransitionException;
use Livewire\Component;

/**
 * PenempatanSiswa Component (Admin Namespace)
 *
 * Modal component for admin to place students into target groups.
 * Handles two scenarios:
 * 1. Promoted students (usulan_id provided) - Students approved for level promotion
 * 2. New students (siswa_id provided) - New students from placement tests
 *
 * Displays student info and available groups filtered by tingkat and jenis_kelamin,
 * with capacity validation to prevent placing into full groups.
 *
 * Task 15.1: Implement PenempatanSiswa component (Admin namespace)
 * Requirements: 5.4, 6.1, 6.2, 6.3, 12.2, 12.3, 12.4, 12.5
 */
class PenempatanSiswa extends Component
{
    // ─── Properties ──────────────────────────────────────────────────────────

    // Scenario identifiers (mutually exclusive)
    public ?int $usulanId = null;      // For promoted students
    public ?int $siswaId = null;       // For new students

    // Loaded data
    public ?UsulanKenaikanTingkat $usulan = null;
    public ?Siswa $siswa = null;
    public $availableGroups = [];

    // Student info (populated from either usulan or siswa)
    public string $studentName = '';
    public string $studentNIS = '';
    public string $studentGender = '';
    public string $targetTingkat = '';

    // Form input
    public string $tanggal_penempatan = '';

    // UI state
    public bool $showModal = false;

    // ─── Listeners ───────────────────────────────────────────────────────────

    protected $listeners = [
        'open-placement-modal' => 'openModalForUsulan',
        'open-placement-modal-new-student' => 'openModalForNewStudent',
    ];

    // ─── Modal Management ────────────────────────────────────────────────────

    /**
     * Open modal for promoted student placement
     *
     * Scenario 1: Student with approved usulan (status "Disetujui")
     *
     * @param int $usulanId Proposal ID for placement
     */
    public function openModalForUsulan(int $usulanId): void
    {
        $this->resetForm();
        $this->usulanId = $usulanId;
        $this->tanggal_penempatan = now()->format('Y-m-d'); // Set default to today
        $this->loadUsulanData();
        $this->showModal = true;
    }

    /**
     * Open modal for new student placement
     *
     * Scenario 2: New student from placement test (status_penempatan "Terdaftar")
     *
     * @param int $siswaId Student ID for placement
     */
    public function openModalForNewStudent(int $siswaId): void
    {
        $this->resetForm();
        $this->siswaId = $siswaId;
        $this->tanggal_penempatan = now()->format('Y-m-d'); // Set default to today
        $this->loadNewStudentData();
        $this->showModal = true;
    }

    /**
     * Close modal and reset state
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Reset form fields
     */
    private function resetForm(): void
    {
        $this->usulanId = null;
        $this->siswaId = null;
        $this->usulan = null;
        $this->siswa = null;
        $this->availableGroups = [];
        $this->studentName = '';
        $this->studentNIS = '';
        $this->studentGender = '';
        $this->targetTingkat = '';
        $this->tanggal_penempatan = '';
        $this->resetErrorBag();
    }

    // ─── Data Loading ────────────────────────────────────────────────────────

    /**
     * Load data for promoted student placement (Scenario 1)
     *
     * Validates: Requirements 5.4, 6.1, 6.2, 6.3
     */
    private function loadUsulanData(): void
    {
        // Load usulan with related data
        $this->usulan = UsulanKenaikanTingkat::with(['siswa'])
            ->findOrFail($this->usulanId);

        // Populate student info (Req 5.4)
        $this->siswa = $this->usulan->siswa;
        $this->studentName = $this->siswa->nama;
        $this->studentNIS = $this->siswa->nis;
        $this->studentGender = $this->siswa->jenis_kelamin;
        $this->targetTingkat = $this->usulan->tingkat_tujuan->value;

        // Load available groups filtered by tingkat_tujuan and jenis_kelamin (Req 6.1, 6.2, 6.3)
        $this->loadAvailableGroups();
    }

    /**
     * Load data for new student placement (Scenario 2)
     *
     * Validates: Requirements 12.2, 12.3, 12.4
     */
    private function loadNewStudentData(): void
    {
        // Load siswa with tes penempatan result
        $this->siswa = Siswa::with(['tesPenempatan'])
            ->findOrFail($this->siswaId);

        // Populate student info (Req 12.2)
        $this->studentName = $this->siswa->nama;
        $this->studentNIS = $this->siswa->nis;
        $this->studentGender = $this->siswa->jenis_kelamin;

        // Get rekomendasi_tingkat from tes penempatan (Req 12.3)
        if (!$this->siswa->tesPenempatan) {
            throw new \RuntimeException(
                "Data tes penempatan tidak ditemukan untuk siswa {$this->siswa->nama}."
            );
        }

        $this->targetTingkat = $this->siswa->tesPenempatan->rekomendasi_tingkat;

        // Load available groups filtered by rekomendasi_tingkat and jenis_kelamin (Req 12.3, 12.4)
        $this->loadAvailableGroups();
    }

    /**
     * Load available groups from service
     *
     * Validates: Requirements 6.1, 6.2, 6.3
     */
    private function loadAvailableGroups(): void
    {
        $service = app(UsulanKenaikanTingkatServiceInterface::class);

        // Call service to get available groups (Req 6.1, 6.2, 6.3)
        $groups = $service->getAvailableGroups(
            $this->targetTingkat,
            $this->studentGender
        );

        // Transform to array with additional computed data for view
        $this->availableGroups = $groups->map(function ($kelompok) {
            $jumlahSiswa = $kelompok->siswas->count();

            return [
                'id' => $kelompok->id,
                'nama' => $kelompok->nama,
                'guru_pembimbing' => $kelompok->guruPembimbing->nama ?? '-',
                'jumlah_siswa' => $jumlahSiswa,
                'kapasitas_maks' => $kelompok->kapasitas_maks,
                'kapasitas_tersisa' => $kelompok->kapasitas_maks - $jumlahSiswa,
                'is_almost_full' => ($jumlahSiswa / $kelompok->kapasitas_maks) >= 0.8, // 80% capacity warning
            ];
        })->toArray();
    }

    // ─── Placement Actions ───────────────────────────────────────────────────

    /**
     * Place student to selected group
     *
     * Validates: Requirements 6.4, 6.5, 6.6, 12.5
     *
     * @param int $kelompokId Target group ID
     */
    public function placeToGroup(int $kelompokId): void
    {
        // Validate tanggal_penempatan
        $this->validate([
            'tanggal_penempatan' => 'required|date|date_format:Y-m-d',
        ], [
            'tanggal_penempatan.required' => 'Tanggal penempatan wajib diisi',
            'tanggal_penempatan.date' => 'Format tanggal tidak valid',
            'tanggal_penempatan.date_format' => 'Format tanggal harus Y-m-d',
        ]);

        try {
            $service = app(UsulanKenaikanTingkatServiceInterface::class);

            // Scenario 1: Promoted student (usulan_id provided)
            if ($this->usulanId) {
                // Call service to place promoted student (Req 6.4, 6.5, 6.6)
                $service->placeStudent($this->usulanId, $kelompokId, $this->tanggal_penempatan);

                // Success notification
                $this->dispatch('toast', variant: 'sukses', message: "Siswa {$this->studentName} berhasil ditempatkan ke kelompok tujuan.");
                // Refresh parent list
                $this->dispatch('usulan-placed');
            }
            // Scenario 2: New student (siswa_id provided)
            elseif ($this->siswaId) {
                // For new students, we need to place them directly
                // This updates kelompok_id and status_penempatan (Req 12.5)
                $this->placeNewStudent($kelompokId);

                // Success notification
                $this->dispatch('toast', variant: 'sukses', message: "Siswa baru {$this->studentName} berhasil ditempatkan ke kelompok.");
                // Refresh parent list
                $this->dispatch('new-student-placed');
            }

            // Close modal
            $this->closeModal();

        } catch (GroupCapacityExceededException $e) {
            // Handle capacity exceeded error (Req 6.3)
            $this->addError('placement', $e->getMessage());

            // Reload available groups to update capacity info
            $this->loadAvailableGroups();

        } catch (InvalidStatusTransitionException $e) {
            $this->addError('placement', $e->getMessage());

        } catch (\Exception $e) {
            $this->addError('placement', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Place new student directly (Scenario 2 helper)
     *
     * Validates: Requirements 12.5, 12.6
     *
     * @param int $kelompokId Target group ID
     */
    private function placeNewStudent(int $kelompokId): void
    {
        \DB::transaction(function () use ($kelompokId) {
            // Lock kelompok and check capacity
            $kelompok = Kelompok::lockForUpdate()->findOrFail($kelompokId);

            $currentJumlahSiswa = $kelompok->siswas()->count();

            if ($currentJumlahSiswa >= $kelompok->kapasitas_maks) {
                throw new GroupCapacityExceededException(
                    $kelompok->nama,
                    $currentJumlahSiswa,
                    $kelompok->kapasitas_maks
                );
            }

            // Update siswa kelompok_id and status_penempatan (Req 12.5)
            $this->siswa->kelompok_id = $kelompokId;
            $this->siswa->status_penempatan = \App\Enums\StatusPenempatan::DITEMPATKAN;
            $this->siswa->tanggal_penempatan = $this->tanggal_penempatan; // ✅ Record placement date
            $this->siswa->save();

            // Create Riwayat_Kelompok entry with alasan "Penempatan Awal" (Req 12.6)
            \App\Models\RiwayatKelompok::create([
                'siswa_id' => $this->siswa->id,
                'kelompok_dari_id' => null, // New student has no previous group
                'kelompok_ke_id' => $kelompokId,
                'tanggal' => $this->tanggal_penempatan, // Use custom date
                'alasan' => 'Penempatan Awal',
                'disetujui_oleh' => auth()->guard('admin')->id(),
            ]);
        });
    }

    // ─── Helper Methods ──────────────────────────────────────────────────────

    /**
     * Get badge color for capacity status
     *
     * @param array $group Group data array
     * @return array ['bg' => string, 'text' => string]
     */
    public function getCapacityBadgeColor(array $group): array
    {
        $percentage = ($group['jumlah_siswa'] / $group['kapasitas_maks']) * 100;

        if ($percentage >= 80) {
            return [
                'bg' => '#FEF3C7',      // Yellow warning (almost full)
                'text' => '#92400E',
            ];
        }

        return [
            'bg' => '#D1FAE5',          // Green (available)
            'text' => '#065F46',
        ];
    }

    // ─── Render ──────────────────────────────────────────────────────────────

    /**
     * Render the component
     */
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.usulan-kenaikan-tingkat.penempatan-siswa');
    }
}
