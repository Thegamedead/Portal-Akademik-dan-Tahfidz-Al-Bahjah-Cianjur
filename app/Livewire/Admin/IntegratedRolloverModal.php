<?php

namespace App\Livewire\Admin;

use App\Models\TahunAjaran;
use App\Services\IntegratedRolloverService;
use App\Exceptions\RolloverValidationException;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class IntegratedRolloverModal extends Component
{
    /**
     * Controls modal visibility
     */
    public bool $isOpen = false;

    /**
     * ID of the target Tahun Ajaran to activate
     */
    public ?int $targetTahunAjaranId = null;

    /**
     * Impact summary data for confirmation dialog
     * Contains counts of affected students and Tahun Ajaran labels
     */
    public ?array $impactSummary = null;

    /**
     * Error message when calculation or validation fails
     */
    public ?string $errorMessage = null;

    /**
     * Loading state indicator
     */
    public bool $isLoading = false;

    /**
     * Injected IntegratedRolloverService dependency
     */
    protected IntegratedRolloverService $rolloverService;

    /**
     * Livewire listeners
     */
    protected $listeners = [
        'open-integrated-rollover-modal' => 'openModal',
        'confirm-rollback-execution' => 'confirmRollbackExecution',
    ];

    /**
     * Boot the component and inject dependencies
     *
     * Requirements: 8.1
     *
     * @param IntegratedRolloverService $rolloverService
     * @return void
     */
    public function boot(IntegratedRolloverService $rolloverService): void
    {
        $this->rolloverService = $rolloverService;
    }

    /**
     * Open the rollover modal
     *
     * Requirements: 8.1, 8.8
     *
     * @return void
     */
    public function openModal(): void
    {
        $this->isOpen = true;
        $this->targetTahunAjaranId = null;
        $this->impactSummary = null;
    }

    /**
     * Close the rollover modal
     *
     * Requirements: 8.1, 8.8
     *
     * @return void
     */
    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->targetTahunAjaranId = null;
        $this->impactSummary = null;
        $this->isLoading = false;
    }

    /**
     * Load impact summary when target Tahun Ajaran is selected
     *
     * This method is automatically called by Livewire when targetTahunAjaranId changes.
     * It calculates the impact of the rollover operation and stores it for display
     * in the confirmation dialog.
     *
     * Requirements: 8.2, 8.3, 8.4, 8.5, 8.6, 8.7, 8.8
     *
     * @return void
     */
    public function updatedTargetTahunAjaranId(): void
    {
        // Reset impact summary when selection changes
        $this->impactSummary = null;
        $this->errorMessage = null;

        // Only calculate if a target is selected
        if (!$this->targetTahunAjaranId) {
            return;
        }

        try {
            $this->isLoading = true;

            // Calculate impact summary using the service
            $summary = $this->rolloverService->calculateImpactSummary($this->targetTahunAjaranId);

            // Convert DTO to array for Livewire property binding
            $this->impactSummary = $summary->toArray();

        } catch (RolloverValidationException $e) {
            $this->errorMessage = $e->getMessage();
            $this->dispatch('toast', variant: 'error', message: 'Validasi Rollover Gagal: ' . $e->getMessage());

            // Reset target selection
            $this->targetTahunAjaranId = null;

            Log::warning('Rollover impact calculation validation failed', [
                'admin_id' => auth('admin')->id(),
                'target_tahun_ajaran_id' => $this->targetTahunAjaranId,
                'error' => $e->getMessage(),
                'validation_errors' => $e->validationErrors,
            ]);

        } catch (\Exception $e) {
            $this->errorMessage = 'Gagal menghitung dampak rollover: ' . $e->getMessage();
            $this->dispatch('toast', variant: 'error', message: $this->errorMessage);

            // Reset target selection
            $this->targetTahunAjaranId = null;

            Log::error('Rollover impact calculation failed', [
                'admin_id' => auth('admin')->id(),
                'target_tahun_ajaran_id' => $this->targetTahunAjaranId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Execute the rollover process
     *
     * This method is called when the admin confirms the rollover operation.
     * It calls IntegratedRolloverService->executeRollover(), handles success
     * and error scenarios, and dispatches events to update the UI.
     *
     * Requirements: 8.9, 10.1, 10.2, 10.3, 10.4, 10.5, 10.6, 10.7, 10.8
     *
     * @return void
     */
    public function confirmRollover(): void
    {
        // Requirement 10.1: Validate target Tahun Ajaran is selected
        if (!$this->targetTahunAjaranId) {
            $this->dispatch('dialog-open', ['noBackdrop' => true,
                'type' => 'error',
                'title' => 'Validasi Gagal',
                'message' => 'Silakan pilih Tahun Ajaran tujuan'
            ]);
            return;
        }

        try {
            // Requirement 10.1: Set loading state
            $this->isLoading = true;

            // Get authenticated admin ID
            $adminId = auth('admin')->id();

            // Requirement 10.2: Call IntegratedRolloverService->executeRollover()
            Log::info('Admin initiated rollover', [
                'admin_id' => $adminId,
                'target_tahun_ajaran_id' => $this->targetTahunAjaranId,
            ]);

            $result = $this->rolloverService->executeRollover(
                $this->targetTahunAjaranId,
                $adminId
            );

            // Requirement 10.3: Handle success
            if ($result->success) {
                Log::info('Rollover completed successfully', [
                    'admin_id' => $adminId,
                    'rollover_log_id' => $result->rolloverLogId,
                    'target_tahun_ajaran' => $result->targetTahunAjaranLabel,
                    'total_affected' => $result->getTotalAffected(),
                ]);

                // Requirement 10.3: Dispatch success dialog event
                // Requirement 10.4: Display new active Tahun Ajaran label
                // Requirement 10.5: Display total count of students processed
                // Requirement 10.6: Display count of students promoted to Kelas 8
                // Requirement 10.7: Display count of students promoted to Kelas 9
                // Requirement 10.8: Display count of students marked as "Lulus SMP"
                $this->dispatch('dialog-open', ['noBackdrop' => true,
                    'type' => 'success',
                    'title' => 'Rollover Berhasil',
                    'message' => sprintf(
                        "Sistem berhasil bermigrasi ke %s.\n\n" .
                        "Detail:\n" .
                        "- Total siswa diproses: %d siswa\n" .
                        "- Kelas 7 → 8: %d siswa\n" .
                        "- Kelas 8 → 9: %d siswa\n" .
                        "- Lulus SMP: %d siswa\n" .
                        "- Kelompok di-clear: %d siswa",
                        $result->targetTahunAjaranLabel,
                        $result->getTotalAffected(),
                        $result->academicResult->kelas7To8Count,
                        $result->academicResult->kelas8To9Count,
                        $result->academicResult->graduatedCount,
                        $result->tahfidzResult->clearedCount
                    )
                ]);

                // Close the modal
                $this->closeModal();

                // Requirement 10.8: Refresh UI components to reflect new Tahun Ajaran
                $this->dispatch('rollover-completed', [
                    'target_tahun_ajaran_id' => $result->targetTahunAjaranId,
                    'target_tahun_ajaran_label' => $result->targetTahunAjaranLabel,
                ]);
            }

        } catch (\App\Exceptions\RolloverLockException $e) {
            // Requirement 20.2: Handle concurrent rollover attempt
            Log::warning('Rollover lock conflict during confirmation', [
                'admin_id' => auth('admin')->id(),
                'target_tahun_ajaran_id' => $this->targetTahunAjaranId,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('dialog-open', ['noBackdrop' => true,
                'type' => 'warning',
                'title' => 'Rollover Sedang Berjalan',
                'message' => $e->getMessage()
            ]);

        } catch (\App\Exceptions\RolloverValidationException $e) {
            // Requirement 7.7: Handle validation errors
            Log::warning('Rollover validation failed during confirmation', [
                'admin_id' => auth('admin')->id(),
                'target_tahun_ajaran_id' => $this->targetTahunAjaranId,
                'error' => $e->getMessage(),
                'validation_errors' => $e->validationErrors,
            ]);

            $this->dispatch('dialog-open', ['noBackdrop' => true,
                'type' => 'error',
                'title' => 'Validasi Gagal',
                'message' => $e->getMessage()
            ]);

        } catch (\Exception $e) {
            // Requirement 9.7: Handle all other exceptions
            Log::error('Rollover execution failed during confirmation', [
                'admin_id' => auth('admin')->id(),
                'target_tahun_ajaran_id' => $this->targetTahunAjaranId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('dialog-open', ['noBackdrop' => true,
                'type' => 'error',
                'title' => 'Rollover Gagal',
                'message' => 'Terjadi kesalahan saat melakukan rollover: ' . $e->getMessage()
            ]);

        } finally {
            // Always disable loading state
            $this->isLoading = false;
        }
    }

    /**
     * Execute rollback of the most recent rollover
     *
     * This method retrieves the most recent completed rollover from the audit log,
     * validates that it's eligible for rollback (completed within 24 hours),
     * displays a confirmation dialog with warning, and calls the service layer
     * to execute the rollback operation.
     *
     * Requirements: 12.1, 12.2
     *
     * @return void
     */
    public function executeRollback(): void
    {
        try {
            $this->isLoading = true;

            // Requirement 12.1: Get most recent rollover log from audit table
            $mostRecentRollover = \App\Models\RolloverAuditLog::where('status', 'completed')
                ->whereNull('rolled_back_at')
                ->orderBy('completed_at', 'desc')
                ->first();

            // Validate rollback eligibility
            if (!$mostRecentRollover) {
                $this->dispatch('dialog-open', ['noBackdrop' => true,
                    'type' => 'warning',
                    'title' => 'Tidak Ada Rollover',
                    'message' => 'Tidak ada rollover yang dapat di-rollback. Pastikan rollover sudah selesai dan belum pernah di-rollback.'
                ]);

                Log::info('Rollback attempt failed: No eligible rollover found', [
                    'admin_id' => auth('admin')->id(),
                ]);

                return;
            }

            // Check if rollover is within 24 hours
            if (!$mostRecentRollover->canRollback()) {
                $this->dispatch('dialog-open', ['noBackdrop' => true,
                    'type' => 'warning',
                    'title' => 'Waktu Rollback Habis',
                    'message' => sprintf(
                        'Rollback hanya dapat dilakukan dalam 24 jam setelah rollover. ' .
                        'Rollover terakhir dilakukan pada %s.',
                        $mostRecentRollover->completed_at->format('d M Y H:i:s')
                    )
                ]);

                Log::info('Rollback attempt failed: Time limit exceeded', [
                    'admin_id' => auth('admin')->id(),
                    'rollover_log_id' => $mostRecentRollover->id,
                    'completed_at' => $mostRecentRollover->completed_at,
                    'hours_since' => $mostRecentRollover->completed_at->diffInHours(now()),
                ]);

                return;
            }

            // Requirement 12.2: Show confirmation dialog with warning
            // Dispatch a confirmation request to the frontend
            $this->dispatch('confirm-rollback', [
                'rolloverLogId' => $mostRecentRollover->id,
                'sourceTahunAjaran' => $mostRecentRollover->sourceTahunAjaran->label,
                'targetTahunAjaran' => $mostRecentRollover->targetTahunAjaran->label,
                'completedAt' => $mostRecentRollover->completed_at->format('d M Y H:i:s'),
                'totalAffected' => $mostRecentRollover->totalAffected,
                'kelas7To8Count' => $mostRecentRollover->kelas_7_to_8_count,
                'kelas8To9Count' => $mostRecentRollover->kelas_8_to_9_count,
                'graduatedCount' => $mostRecentRollover->graduated_count,
                'kelompokClearedCount' => $mostRecentRollover->kelompok_cleared_count,
            ]);

            Log::info('Rollback confirmation dialog displayed', [
                'admin_id' => auth('admin')->id(),
                'rollover_log_id' => $mostRecentRollover->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to prepare rollback', [
                'admin_id' => auth('admin')->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('dialog-open', ['noBackdrop' => true,
                'type' => 'error',
                'title' => 'Kesalahan',
                'message' => 'Gagal mempersiapkan rollback: ' . $e->getMessage()
            ]);

        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Confirm and execute the rollback operation
     *
     * This method is called after the user confirms the rollback in the dialog.
     * It calls IntegratedRolloverService->rollback() and handles success/error scenarios.
     *
     * Requirements: 12.1, 12.2
     *
     * @param int $rolloverLogId The ID of the rollover to rollback
     * @return void
     */
    public function confirmRollbackExecution(int $rolloverLogId): void
    {
        try {
            $this->isLoading = true;

            // Get authenticated admin ID
            $adminId = auth('admin')->id();

            Log::info('Admin confirmed rollback execution', [
                'admin_id' => $adminId,
                'rollover_log_id' => $rolloverLogId,
            ]);

            // Requirement 12.1: Call IntegratedRolloverService->rollback()
            $result = $this->rolloverService->rollback($rolloverLogId, $adminId);

            // Requirement 12.2: Handle success
            Log::info('Rollback completed successfully', [
                'admin_id' => $adminId,
                'rollover_log_id' => $rolloverLogId,
                'restored_tahun_ajaran' => $result['source_tahun_ajaran_label'],
            ]);

            // Display success notification
            $this->dispatch('dialog-open', ['noBackdrop' => true,
                'type' => 'success',
                'title' => 'Rollback Berhasil',
                'message' => sprintf(
                    "Rollback berhasil dilakukan. Sistem dikembalikan ke %s.\n\n" .
                    "Detail:\n" .
                    "- Siswa di-revert dari Kelas 8 → 7: %d siswa\n" .
                    "- Siswa di-revert dari Kelas 9 → 8: %d siswa\n" .
                    "- Siswa di-revert dari Lulus SMP → Aktif: %d siswa\n" .
                    "- Kelompok dipulihkan: %d siswa\n" .
                    "- Riwayat kelompok dihapus: %d records",
                    $result['source_tahun_ajaran_label'],
                    $result['reverted_kelas_8_count'] ?? 0,
                    $result['reverted_kelas_9_count'] ?? 0,
                    $result['reverted_graduated_count'] ?? 0,
                    $result['restored_kelompok_count'] ?? 0,
                    $result['deleted_riwayat_count'] ?? 0
                )
            ]);

            // Close the modal
            $this->closeModal();

            // Refresh UI components to reflect restored Tahun Ajaran
            $this->dispatch('rollback-completed', [
                'restored_tahun_ajaran_id' => $result['source_tahun_ajaran_id'],
                'restored_tahun_ajaran_label' => $result['source_tahun_ajaran_label'],
            ]);

        } catch (\App\Exceptions\RolloverLockException $e) {
            Log::warning('Rollback lock conflict', [
                'admin_id' => auth('admin')->id(),
                'rollover_log_id' => $rolloverLogId,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('dialog-open', ['noBackdrop' => true,
                'type' => 'warning',
                'title' => 'Operasi Sedang Berjalan',
                'message' => $e->getMessage()
            ]);

        } catch (\App\Exceptions\RolloverValidationException $e) {
            Log::warning('Rollback validation failed', [
                'admin_id' => auth('admin')->id(),
                'rollover_log_id' => $rolloverLogId,
                'error' => $e->getMessage(),
                'validation_errors' => $e->validationErrors,
            ]);

            $this->dispatch('dialog-open', ['noBackdrop' => true,
                'type' => 'error',
                'title' => 'Validasi Gagal',
                'message' => $e->getMessage()
            ]);

        } catch (\Exception $e) {
            Log::error('Rollback execution failed', [
                'admin_id' => auth('admin')->id(),
                'rollover_log_id' => $rolloverLogId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('dialog-open', ['noBackdrop' => true,
                'type' => 'error',
                'title' => 'Rollback Gagal',
                'message' => 'Terjadi kesalahan saat melakukan rollback: ' . $e->getMessage()
            ]);

        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Render the component
     *
     * @return \Illuminate\View\View
     */
    public function render(): \Illuminate\View\View
    {
        $currentTa = TahunAjaran::getActive();
        $availableTa = TahunAjaran::where('id', '!=', $currentTa?->id)
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        // Get most recent rollover for rollback button visibility
        $mostRecentRollover = \App\Models\RolloverAuditLog::where('status', 'completed')
            ->whereNull('rolled_back_at')
            ->orderBy('completed_at', 'desc')
            ->first();

        return view('livewire.admin.integrated-rollover-modal', [
            'currentTa' => $currentTa,
            'availableTa' => $availableTa,
            'mostRecentRollover' => $mostRecentRollover,
            'canRollback' => $mostRecentRollover && $mostRecentRollover->canRollback(),
        ]);
    }
}

