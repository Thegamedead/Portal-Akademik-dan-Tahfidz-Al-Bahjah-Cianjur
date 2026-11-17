<?php

namespace App\Livewire\Admin\UsulanKenaikanTingkat;

use App\Models\UsulanKenaikanTingkat;
use App\Models\ParameterKonfigurasi;
use App\Services\Contracts\UsulanKenaikanTingkatServiceInterface;
use App\Exceptions\InvalidStatusTransitionException;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

/**
 * ReviewUsulan Component (Admin Namespace)
 *
 * Modal component for admin to approve or reject promotion proposals
 * with status "Diajukan". Displays proposal details, student progress,
 * and provides decision buttons with catatan_keputusan textarea.
 *
 * Task 14.1: Implement ReviewUsulan component (Admin namespace)
 * Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6
 */
class ReviewUsulan extends Component
{
    // ─── Properties ──────────────────────────────────────────────────────────

    public ?int $usulanId = null;
    public ?UsulanKenaikanTingkat $usulan = null;

    // Form fields
    public string $tanggal_keputusan = '';
    public string $catatan_keputusan = '';

    // UI state
    public bool $showModal = false;
    public string $action = ''; // 'approve' or 'reject'

    // ─── Listeners ───────────────────────────────────────────────────────────

    protected $listeners = [
        'open-review-modal' => 'openModal',
    ];

    // ─── Modal Management ────────────────────────────────────────────────────

    /**
     * Open review modal with usulan data
     *
     * @param int $usulanId Proposal ID to review
     */
    public function openModal(int $usulanId): void
    {
        $this->resetForm();
        $this->usulanId = $usulanId;
        $this->tanggal_keputusan = now()->format('Y-m-d');
        $this->loadUsulan();
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
        $this->usulan = null;
        $this->tanggal_keputusan = '';
        $this->catatan_keputusan = '';
        $this->action = '';
        $this->resetErrorBag();
    }

    // ─── Data Loading ────────────────────────────────────────────────────────

    /**
     * Load usulan with related data
     *
     * Validates: Requirements 4.1
     */
    private function loadUsulan(): void
    {
        $this->usulan = UsulanKenaikanTingkat::with([
            'siswa.progresHafalans', // Load all progres (now lifetime/accumulative)
            'siswa',
            'guruPengusul',
            'kelompokAsal',
        ])->findOrFail($this->usulanId);
    }

    /**
     * Get required juz threshold for target level
     *
     * Validates: Requirements 4.1
     */
    public function getRequiredThreshold(): int
    {
        if (!$this->usulan) {
            return 0;
        }

        $config = ParameterKonfigurasi::getInstance();
        $batasTingkat = $config->batas_tingkat;

        // Map tingkat to array key
        $tingkatKey = $this->usulan->tingkat_tujuan->value;

        // batas_tingkat structure: {"Dasar": [1,2,3], "Menengah": [11,12,13], "Atas": [21,22,23]}
        // Get CUMULATIVE count: Dasar + Menengah untuk masuk Menengah, Dasar + Menengah + Atas untuk masuk Atas
        if (isset($batasTingkat[$tingkatKey]) && is_array($batasTingkat[$tingkatKey])) {
            // For Dasar: no requirement (0 juz)
            if ($tingkatKey === 'Dasar') {
                return 0;
            }

            // For Menengah: count all juz in Dasar
            if ($tingkatKey === 'Menengah') {
                $juzDasar = $batasTingkat['Dasar'] ?? [];
                return count($juzDasar);
            }

            // For Atas: count all juz in Dasar + Menengah
            if ($tingkatKey === 'Atas') {
                $juzDasar = $batasTingkat['Dasar'] ?? [];
                $juzMenengah = $batasTingkat['Menengah'] ?? [];
                return count($juzDasar) + count($juzMenengah);
            }
        }

        return 0;
    }

    /**
     * Get student's total juz tuntas
     *
     * Since progres_hafalans is now lifetime/accumulative (no tahun_ajaran_id),
     * we just get the single progres record for this student.
     *
     * Validates: Requirements 4.1
     */
    public function getTotalJuzTuntas(): int
    {
        if (!$this->usulan || !$this->usulan->siswa) {
            return 0;
        }

        // Progres hafalan is now lifetime/accumulative per siswa
        // One siswa = one progres record (unique on siswa_id)
        $progres = $this->usulan->siswa->progresHafalans()->first();

        return $progres ? ($progres->total_juz_tuntas ?? 0) : 0;
    }

    /**
     * Check if student has progres hafalan data
     */
    public function hasProgresData(): bool
    {
        if (!$this->usulan || !$this->usulan->siswa) {
            return false;
        }

        return $this->usulan->siswa->progresHafalans()->exists();
    }

    /**
     * Get info message about progres data source
     */
    public function getProgresDataInfo(): string
    {
        if (!$this->usulan || !$this->usulan->siswa) {
            return '';
        }

        $progres = $this->usulan->siswa->progresHafalans()->first();

        if ($progres && $progres->updated_at) {
            return "Data hafalan akumulatif (terakhir diperbarui: " . $progres->updated_at->format('d M Y') . ")";
        }

        return "Data hafalan akumulatif";
    }

    // ─── Decision Actions ────────────────────────────────────────────────────

    /**
     * Approve promotion proposal
     *
     * Validates: Requirements 4.2, 4.5
     */
    public function approve(): void
    {
        $this->validate([
            'tanggal_keputusan' => 'required|date',
        ], [
            'tanggal_keputusan.required' => 'Tanggal keputusan wajib diisi.',
            'tanggal_keputusan.date' => 'Format tanggal keputusan tidak valid.',
        ]);

        try {
            // Get current admin ID
            $adminId = auth()->guard('admin')->id();

            // Call service to approve usulan (Requirement 4.2)
            $service = app(UsulanKenaikanTingkatServiceInterface::class);
            $service->approveUsulan(
                $this->usulanId,
                $adminId,
                !empty($this->catatan_keputusan) ? $this->catatan_keputusan : null, // Requirement 4.5: optional
                $this->tanggal_keputusan
            );

            // Show success notification (Requirement 4.6)
            $this->dispatch('toast', variant: 'sukses', message: 'Usulan berhasil disetujui.');
            // Refresh parent table
            $this->dispatch('usulan-processed');

            // Close modal
            $this->closeModal();

        } catch (InvalidStatusTransitionException $e) {
            $this->addError('action', $e->getMessage());
            // Force re-render to reset loading state
            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            $this->addError('action', 'Terjadi kesalahan: ' . $e->getMessage());
            // Force re-render to reset loading state
            $this->dispatch('$refresh');
        }
    }

    /**
     * Reject promotion proposal
     *
     * Validates: Requirements 4.3, 4.4
     */
    public function reject(): void
    {
        // Validate catatan_keputusan is required for rejection (Requirement 4.4)
        $this->validate([
            'tanggal_keputusan' => 'required|date',
            'catatan_keputusan' => 'required|string|min:10',
        ], [
            'tanggal_keputusan.required' => 'Tanggal keputusan wajib diisi.',
            'tanggal_keputusan.date' => 'Format tanggal keputusan tidak valid.',
            'catatan_keputusan.required' => 'Catatan keputusan wajib diisi saat menolak usulan.',
            'catatan_keputusan.min' => 'Catatan keputusan minimal 10 karakter.',
        ]);

        try {
            // Get current admin ID
            $adminId = auth()->guard('admin')->id();

            // Call service to reject usulan (Requirement 4.3)
            $service = app(UsulanKenaikanTingkatServiceInterface::class);
            $service->rejectUsulan(
                $this->usulanId,
                $adminId,
                $this->catatan_keputusan,
                $this->tanggal_keputusan
            );

            // Show success notification (Requirement 4.6)
            $this->dispatch('toast', variant: 'sukses', message: 'Usulan berhasil ditolak.');
            // Refresh parent table
            $this->dispatch('usulan-processed');

            // Close modal
            $this->closeModal();

        } catch (InvalidStatusTransitionException $e) {
            $this->addError('action', $e->getMessage());
            // Force re-render to reset loading state
            $this->dispatch('$refresh');
        } catch (ValidationException $e) {
            $this->addError('catatan_keputusan', $e->getMessage());
            // Force re-render to reset loading state
            $this->dispatch('$refresh');
        } catch (\Exception $e) {
            $this->addError('action', 'Terjadi kesalahan: ' . $e->getMessage());
            // Force re-render to reset loading state
            $this->dispatch('$refresh');
        }
    }

    // ─── Render ──────────────────────────────────────────────────────────────

    /**
     * Render the component
     */
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.usulan-kenaikan-tingkat.review-usulan', [
            'requiredThreshold' => $this->getRequiredThreshold(),
            'totalJuzTuntas' => $this->getTotalJuzTuntas(),
            'hasProgresData' => $this->hasProgresData(),
            'progresDataInfo' => $this->getProgresDataInfo(),
        ]);
    }
}
