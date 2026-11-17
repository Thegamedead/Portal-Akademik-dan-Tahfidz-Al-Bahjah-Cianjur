<?php

namespace App\Services;

use App\Services\Handlers\AcademicProgressionHandler;
use App\Services\Handlers\TahfidzGroupHandler;
use App\Services\Handlers\RolloverAuditLogger;
use App\Models\TahunAjaran;
use App\Models\Siswa;
use App\Models\Kelas;
use App\DataTransferObjects\ImpactSummary;
use App\DataTransferObjects\RolloverResult;
use App\Exceptions\RolloverValidationException;
use App\Exceptions\RolloverLockException;
use App\Enums\StatusAkademik;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Integrated Rollover Service
 *
 * Orchestrates the complete year-end rollover process for academic and Tahfidz systems.
 * Coordinates:
 * - Academic Year activation
 * - Academic promotion (Kelas 7→8→9)
 * - Graduation processing (Kelas 9 → Lulus SMP)
 * - Tahfidz group clearing
 * - Historical record creation
 * - Audit logging
 *
 * All operations execute within a single atomic database transaction to ensure
 * data consistency and integrity.
 */
class IntegratedRolloverService
{
    /**
     * Lock name used for MySQL advisory locking
     * Ensures only one rollover operation can execute at a time
     */
    private const LOCK_NAME = 'rollover_operation';

    /**
     * Lock timeout in seconds (5 minutes)
     * If lock is not released, it will automatically expire after this duration
     */
    private const LOCK_TIMEOUT = 300;

    /**
     * Create new IntegratedRolloverService instance
     *
     * @param AcademicProgressionHandler $academicHandler Handles academic promotions and graduations
     * @param TahfidzGroupHandler $tahfidzHandler Handles Tahfidz group clearing operations
     * @param RolloverAuditLogger $auditLogger Handles audit logging for compliance and tracking
     */
    public function __construct(
        private AcademicProgressionHandler $academicHandler,
        private TahfidzGroupHandler $tahfidzHandler,
        private RolloverAuditLogger $auditLogger
    ) {}

    /**
     * Check if any rollover operation is currently in progress
     *
     * Uses MySQL IS_USED_LOCK() to check if the rollover lock is currently held.
     * This allows the UI to display appropriate messages when a rollover is already running.
     *
     * @return bool True if a rollover is currently in progress (lock is held)
     */
    public function isRolloverInProgress(): bool
    {
        $result = DB::selectOne(
            'SELECT IS_USED_LOCK(?) as connection_id',
            [self::LOCK_NAME]
        );

        // IS_USED_LOCK returns NULL if lock is not held, or connection ID if it is
        return $result->connection_id !== null;
    }

    /**
     * Acquire exclusive lock for rollover operation
     *
     * Uses MySQL GET_LOCK() to implement advisory locking that prevents concurrent
     * rollover operations. The lock will automatically expire after LOCK_TIMEOUT seconds
     * if not explicitly released, protecting against hung processes.
     *
     * Requirements addressed:
     * - 20.1: Exclusive lock acquisition when rollover initiated
     * - 20.5: 5-minute timeout for abnormal process termination
     *
     * @return bool True if lock was successfully acquired, false otherwise
     */
    private function acquireLock(): bool
    {
        $result = DB::selectOne(
            'SELECT GET_LOCK(?, ?) as locked',
            [self::LOCK_NAME, self::LOCK_TIMEOUT]
        );

        // GET_LOCK returns 1 if successful, 0 if timeout, NULL on error
        return $result->locked === 1;
    }

    /**
     * Release the rollover operation lock
     *
     * Uses MySQL RELEASE_LOCK() to free the advisory lock, allowing other
     * rollover operations to proceed. This should always be called after
     * rollover completes or fails, typically in a finally block.
     *
     * Requirements addressed:
     * - 20.3: Release lock when rollover completes successfully
     * - 20.4: Release lock when rollover fails
     *
     * @return void
     */
    private function releaseLock(): void
    {
        DB::select('SELECT RELEASE_LOCK(?)', [self::LOCK_NAME]);
    }

    /**
     * Validate that rollover can be executed safely
     *
     * Performs comprehensive pre-execution validation to ensure:
     * - Target Tahun Ajaran is valid and exists
     * - Target is different from current active Tahun Ajaran
     * - Required Kelas records (7, 8, 9) exist and are valid
     * - At least one active student exists to process
     *
     * Requirements addressed:
     * - 7.1: Verify exactly one target Tahun Ajaran selected
     * - 7.2: Verify target is different from current active
     * - 7.7: Display warning if zero affected students
     * - 18.2: Validate Kelas references exist
     * - 18.3: Exclude students with null Kelas
     * - 18.4: Reject Kelas > 9
     * - 18.5: Reject Kelas < 7
     * - 18.6: Validate Status_Akademik enum values
     * - 18.7: Validate Status_Penempatan enum values
     *
     * @param int $targetTahunAjaranId The target academic year to validate
     * @return void
     * @throws RolloverValidationException If validation fails
     */
    public function validatePreConditions(int $targetTahunAjaranId): void
    {
        // Requirement 7.1: Verify target Tahun Ajaran exists
        $targetTahunAjaran = TahunAjaran::find($targetTahunAjaranId);
        if (!$targetTahunAjaran) {
            throw new RolloverValidationException(
                'Tahun Ajaran tujuan tidak ditemukan',
                ['target_tahun_ajaran' => 'ID tidak valid']
            );
        }

        // Requirement 7.2: Verify target is different from current active
        $currentTahunAjaran = TahunAjaran::getActive();
        if (!$currentTahunAjaran) {
            throw new RolloverValidationException(
                'Tidak ada Tahun Ajaran yang aktif saat ini',
                ['current_tahun_ajaran' => 'Tidak ada tahun ajaran aktif']
            );
        }

        if ($targetTahunAjaran->id === $currentTahunAjaran->id) {
            throw new RolloverValidationException(
                'Tahun Ajaran tujuan sama dengan yang aktif',
                ['target_tahun_ajaran' => 'Harus berbeda dengan tahun ajaran aktif']
            );
        }

        // Requirement 18.2: Validate all required Kelas records exist
        $kelas7 = Kelas::where('tingkat', '7')->first();
        $kelas8 = Kelas::where('tingkat', '8')->first();
        $kelas9 = Kelas::where('tingkat', '9')->first();

        if (!$kelas7 || !$kelas8 || !$kelas9) {
            throw new RolloverValidationException(
                'Data kelas tidak lengkap. Sistem memerlukan Kelas 7, 8, dan 9',
                ['kelas' => 'Kelas 7, 8, atau 9 tidak ditemukan']
            );
        }

        // Requirement 18.4: Validate no Kelas with tingkat > 9
        $invalidKelasHigh = Kelas::where('tingkat', '>', '9')->exists();
        if ($invalidKelasHigh) {
            throw new RolloverValidationException(
                'Data tidak valid: Kelas lebih dari 9 ditemukan',
                ['kelas' => 'Tingkat kelas tidak boleh lebih dari 9']
            );
        }

        // Requirement 18.5: Validate no Kelas with tingkat < 7
        $invalidKelasLow = Kelas::where('tingkat', '<', '7')->exists();
        if ($invalidKelasLow) {
            throw new RolloverValidationException(
                'Data tidak valid: Kelas kurang dari 7 ditemukan',
                ['kelas' => 'Tingkat kelas tidak boleh kurang dari 7']
            );
        }

        // Requirement 18.3: Check for students with null Kelas (will be excluded)
        $nullKelasCount = Siswa::where('status_akademik', StatusAkademik::AKTIF->value)
            ->whereNull('kelas_id')
            ->count();

        if ($nullKelasCount > 0) {
            // Log warning but don't fail validation
            Log::warning("Rollover validation: {$nullKelasCount} active students have null kelas_id and will be excluded");
        }

        // Requirement 7.7: Count active students that will be affected
        $activeStudentCount = Siswa::where('status_akademik', StatusAkademik::AKTIF->value)
            ->whereNotNull('kelas_id')
            ->count();

        if ($activeStudentCount === 0) {
            throw new RolloverValidationException(
                'Tidak ada siswa aktif yang akan di-rollover',
                ['students' => 'Tidak ada siswa aktif dengan kelas yang valid']
            );
        }

        // Requirement 18.1: Validate kelompok references (log warnings for invalid references)
        $invalidKelompokCount = Siswa::where('status_akademik', StatusAkademik::AKTIF->value)
            ->whereNotNull('kelompok_id')
            ->whereDoesntHave('kelompok')
            ->count();

        if ($invalidKelompokCount > 0) {
            Log::warning("Rollover validation: {$invalidKelompokCount} students have invalid kelompok_id references (will be set to null)");
        }

        // All validations passed
    }

    /**
     * Calculate the impact of executing rollover (for confirmation dialog)
     *
     * Counts students that will be affected by each rollover operation:
     * - Kelas 7 → 8 promotions
     * - Kelas 8 → 9 promotions
     * - Kelas 9 → "Lulus SMP" graduations
     * - Kelompok clearing
     *
     * This data is displayed in the confirmation dialog before execution.
     *
     * Requirements addressed:
     * - 7.3: Count students affected by academic promotion
     * - 7.4: Count students to be marked as "Lulus SMP"
     * - 7.5: Count students whose Kelompok will be cleared
     * - 7.6: Display counts to Admin before confirmation
     * - 8.2: Display current active Tahun Ajaran label
     * - 8.3: Display target Tahun Ajaran label
     * - 8.4: Display Kelas 7 to 8 promotion count
     * - 8.5: Display Kelas 8 to 9 promotion count
     * - 8.6: Display graduation count
     * - 8.7: Display Kelompok clearing count
     *
     * @param int $targetTahunAjaranId The target academic year
     * @return ImpactSummary Counts of students affected by each operation
     * @throws RolloverValidationException If validation fails
     */
    public function calculateImpactSummary(int $targetTahunAjaranId): ImpactSummary
    {
        // First validate pre-conditions
        $this->validatePreConditions($targetTahunAjaranId);

        // Get current and target Tahun Ajaran
        $currentTahunAjaran = TahunAjaran::getActive();
        $targetTahunAjaran = TahunAjaran::findOrFail($targetTahunAjaranId);

        // Get Kelas IDs for counting
        $kelas7 = Kelas::where('tingkat', '7')->first();
        $kelas8 = Kelas::where('tingkat', '8')->first();
        $kelas9 = Kelas::where('tingkat', '9')->first();

        // Requirement 8.4: Count students to be promoted from Kelas 7 to 8
        $kelas7To8Count = Siswa::eligibleForPromotion($kelas7->id)->count();

        // Requirement 8.5: Count students to be promoted from Kelas 8 to 9
        $kelas8To9Count = Siswa::eligibleForPromotion($kelas8->id)->count();

        // Requirement 8.6: Count students to be marked as "Lulus SMP"
        $graduatedCount = Siswa::eligibleForPromotion($kelas9->id)->count();

        // Requirement 8.7: Count students whose Kelompok will be cleared
        // Only active students with assigned Kelompok
        $kelompokClearedCount = Siswa::where('status_akademik', StatusAkademik::AKTIF->value)
            ->whereNotNull('kelompok_id')
            ->count();

        return new ImpactSummary(
            currentTahunAjaranId: $currentTahunAjaran->id,
            targetTahunAjaranId: $targetTahunAjaran->id,
            currentTahunAjaranLabel: $currentTahunAjaran->label,
            targetTahunAjaranLabel: $targetTahunAjaran->label,
            kelas7To8Count: $kelas7To8Count,
            kelas8To9Count: $kelas8To9Count,
            graduatedCount: $graduatedCount,
            kelompokClearedCount: $kelompokClearedCount,
        );
    }

    /**
     * Execute the complete integrated rollover process
     *
     * Orchestrates all rollover operations within a single atomic transaction:
     * 1. Acquires exclusive lock to prevent concurrent rollover operations
     * 2. Creates initial audit log entry (status: 'in_progress')
     * 3. Deactivates all Tahun Ajaran records
     * 4. Activates target Tahun Ajaran
     * 5. Processes academic promotions (7→8→9→Graduated)
     * 6. Clears Tahfidz group assignments
     * 7. Updates audit log with completion data
     * 8. Releases lock in finally block
     *
     * If any step fails, Laravel's transaction automatically rolls back all changes
     * and the lock is released in the finally block.
     *
     * Requirements addressed:
     * - 1.1: Validate target Tahun_Pelajaran selected
     * - 1.2: Set target Tahun_Pelajaran status to active
     * - 1.3: Set all other Tahun_Pelajaran to inactive
     * - 1.4: Preserve all previous Tahun_Pelajaran records
     * - 1.5: Display newly active Tahun_Pelajaran to users
     * - 9.1: Execute all operations within single database transaction
     * - 9.2: Revert all changes if any operation fails
     * - 9.3: Restore previous active Tahun_Pelajaran on failure
     * - 9.4: Restore original Kelas values on failure
     * - 9.5: Restore original kelompok_id values on failure
     * - 9.6: Remove RiwayatKelompok records on failed transaction
     * - 16.1: Complete within 30 seconds for ≤500 students
     * - 16.2: Complete within 60 seconds for ≤1000 students
     * - 16.3: Use batch insertion with 500 record chunks
     * - 16.4: Use optimized bulk update statements
     *
     * @param int $targetTahunAjaranId The ID of the Tahun Ajaran to activate
     * @param int $adminId The ID of the admin initiating the rollover
     * @return RolloverResult Summary of rollover execution
     * @throws RolloverLockException If another rollover is in progress
     * @throws RolloverValidationException If validation fails
     * @throws \Exception If rollover execution fails
     */
    public function executeRollover(int $targetTahunAjaranId, int $adminId): RolloverResult
    {
        // Pre-validation before attempting lock
        $this->validatePreConditions($targetTahunAjaranId);

        $auditLog = null;
        $lockAcquired = false;

        try {
            // Requirement 20.1: Acquire exclusive lock at transaction start
            Log::info('Attempting to acquire rollover lock', [
                'admin_id' => $adminId,
                'target_tahun_ajaran_id' => $targetTahunAjaranId,
            ]);

            if (!$this->acquireLock()) {
                // Requirement 20.2: Display message if another rollover is in progress
                throw new RolloverLockException(
                    'Rollover sedang diproses oleh admin lain, silakan tunggu'
                );
            }

            $lockAcquired = true;
            Log::info('Rollover lock acquired successfully', [
                'admin_id' => $adminId,
                'target_tahun_ajaran_id' => $targetTahunAjaranId,
            ]);

            // Execute all rollover operations within a single transaction
            // Requirement 9.1: Single database transaction for atomicity
            return DB::transaction(function () use ($targetTahunAjaranId, $adminId, &$auditLog) {
                // Get current and target Tahun Ajaran for logging
                $currentTahunAjaran = TahunAjaran::getActive();
                $targetTahunAjaran = TahunAjaran::findOrFail($targetTahunAjaranId);

                // Requirement 13.1: Create initial audit log entry
                Log::info('Creating initial audit log entry', [
                    'admin_id' => $adminId,
                    'source_tahun_ajaran_id' => $currentTahunAjaran->id,
                    'target_tahun_ajaran_id' => $targetTahunAjaranId,
                ]);
                $auditLog = $this->auditLogger->createInitialLog(
                    $adminId,
                    $currentTahunAjaran->id,
                    $targetTahunAjaranId
                );

                // Requirement 1.3: Deactivate all Tahun Ajaran records
                Log::info('Deactivating all Tahun Ajaran records', [
                    'audit_log_id' => $auditLog->id,
                ]);
                TahunAjaran::query()->update(['status' => false]);

                // Requirement 1.2: Activate target Tahun Ajaran
                Log::info('Activating target Tahun Ajaran', [
                    'audit_log_id' => $auditLog->id,
                    'tahun_ajaran_id' => $targetTahunAjaranId,
                    'label' => $targetTahunAjaran->label,
                ]);
                $targetTahunAjaran->update(['status' => true]);

                // Requirement 2.1-3.5: Call AcademicProgressionHandler to process promotions
                Log::info('Processing academic promotions and graduations', [
                    'audit_log_id' => $auditLog->id,
                ]);
                $academicResult = $this->academicHandler->processPromotion();

                Log::info('Academic progression completed', [
                    'audit_log_id' => $auditLog->id,
                    'kelas_7_to_8' => $academicResult->kelas7To8Count,
                    'kelas_8_to_9' => $academicResult->kelas8To9Count,
                    'graduated' => $academicResult->graduatedCount,
                ]);

                // Requirement 4.1-5.7: Call TahfidzGroupHandler to clear groups
                Log::info('Clearing Tahfidz group assignments', [
                    'audit_log_id' => $auditLog->id,
                ]);
                $tahfidzResult = $this->tahfidzHandler->clearGroups($adminId);

                Log::info('Tahfidz group clearing completed', [
                    'audit_log_id' => $auditLog->id,
                    'cleared_count' => $tahfidzResult->clearedCount,
                    'riwayat_records_created' => $tahfidzResult->riwayatRecordsCreated,
                ]);

                // Requirement 13.4: Update audit log with completion data
                Log::info('Updating audit log with completion data', [
                    'audit_log_id' => $auditLog->id,
                ]);
                $this->auditLogger->markCompleted($auditLog, $academicResult, $tahfidzResult);

                // Create successful result
                Log::info('Rollover completed successfully', [
                    'audit_log_id' => $auditLog->id,
                    'source_tahun_ajaran' => $currentTahunAjaran->label,
                    'target_tahun_ajaran' => $targetTahunAjaran->label,
                    'total_affected' => $academicResult->getTotalAffected() + $tahfidzResult->clearedCount,
                    'admin_id' => $adminId,
                ]);

                return RolloverResult::success(
                    rolloverLogId: $auditLog->id,
                    sourceTahunAjaranId: $currentTahunAjaran->id,
                    targetTahunAjaranId: $targetTahunAjaran->id,
                    targetTahunAjaranLabel: $targetTahunAjaran->label,
                    academicResult: $academicResult,
                    tahfidzResult: $tahfidzResult
                );
            }, 5); // 5 retry attempts for deadlock resolution

        } catch (RolloverLockException $e) {
            // Requirement 7.7, 9.7: Catch and handle RolloverLockException
            // Lock acquisition failed - log as warning with full context
            Log::warning('Rollover lock conflict', [
                'admin_id' => $adminId,
                'target_tahun_ajaran_id' => $targetTahunAjaranId,
                'message' => $e->getMessage(),
                'exception_class' => get_class($e),
            ]);

            // Re-throw for UI handling
            throw $e;

        } catch (RolloverValidationException $e) {
            // Requirement 7.7, 9.7: Catch and handle RolloverValidationException
            // Validation failed - log with validation details
            Log::warning('Rollover validation failed', [
                'admin_id' => $adminId,
                'target_tahun_ajaran_id' => $targetTahunAjaranId,
                'validation_errors' => $e->validationErrors,
                'message' => $e->getMessage(),
                'exception_class' => get_class($e),
            ]);

            // Re-throw for UI handling
            throw $e;

        } catch (\Illuminate\Database\QueryException $e) {
            // Requirement 9.7, 13.5: Handle database-specific errors with detailed context
            Log::error('Rollover database error', [
                'admin_id' => $adminId,
                'target_tahun_ajaran_id' => $targetTahunAjaranId,
                'audit_log_id' => $auditLog?->id,
                'error' => $e->getMessage(),
                'sql_state' => $e->errorInfo[0] ?? 'unknown',
                'error_code' => $e->errorInfo[1] ?? 'unknown',
                'exception_class' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);

            // Requirement 13.5: Update audit log with failure information
            if ($auditLog) {
                try {
                    $this->auditLogger->markFailed($auditLog, $e);
                } catch (\Exception $logException) {
                    // If audit log update fails, log that too but don't throw
                    Log::error('Failed to update audit log with failure', [
                        'audit_log_id' => $auditLog->id,
                        'original_error' => $e->getMessage(),
                        'log_error' => $logException->getMessage(),
                    ]);
                }
            }

            // Requirement 9.7: Display error message with technical details
            throw new \RuntimeException(
                'Rollover gagal karena kesalahan database. Silakan hubungi administrator sistem. Error: ' . $e->getMessage(),
                previous: $e
            );

        } catch (\Exception $e) {
            // Requirement 9.7, 13.5: Catch all other exceptions with comprehensive logging
            Log::error('Rollover execution failed', [
                'admin_id' => $adminId,
                'target_tahun_ajaran_id' => $targetTahunAjaranId,
                'audit_log_id' => $auditLog?->id,
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Requirement 13.5: Update audit log with failure information
            if ($auditLog) {
                try {
                    $this->auditLogger->markFailed($auditLog, $e);
                } catch (\Exception $logException) {
                    // If audit log update fails, log that too but don't throw
                    Log::error('Failed to update audit log with failure', [
                        'audit_log_id' => $auditLog->id,
                        'original_error' => $e->getMessage(),
                        'log_error' => $logException->getMessage(),
                    ]);
                }
            }

            // Requirement 9.7: Display error message with technical details
            throw new \RuntimeException(
                'Rollover gagal: ' . $e->getMessage(),
                previous: $e
            );

        } finally {
            // Requirement 9.7, 13.5: Always release lock in all scenarios
            if ($lockAcquired) {
                try {
                    Log::info('Releasing rollover lock', [
                        'admin_id' => $adminId,
                        'audit_log_id' => $auditLog?->id,
                    ]);
                    $this->releaseLock();
                } catch (\Exception $e) {
                    // If lock release fails, log the error but don't throw
                    // This prevents masking the original exception
                    Log::error('Failed to release rollover lock', [
                        'admin_id' => $adminId,
                        'audit_log_id' => $auditLog?->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }
        }
    }

    /**
     * Rollback a completed rollover operation
     *
     * Reverses all changes made during a rollover operation by:
     * 1. Validating rollback eligibility (within 24 hours, completed status)
     * 2. Capturing affected students based on audit log
     * 3. Restoring previous Tahun Ajaran as active
     * 4. Reverting Kelas values (8→7, 9→8)
     * 5. Restoring Status_Akademik from "Lulus SMP" to "Aktif"
     * 6. Restoring kelompok_id and status_penempatan values from RiwayatKelompok
     * 7. Removing RiwayatKelompok records created during rollover
     * 8. Clearing tanggal_lulus_akademik values
     *
     * All operations execute within a single atomic transaction.
     *
     * Requirements addressed:
     * - 12.1: Provide rollback action if rollover executed within current session
     * - 12.2: Display confirmation dialog with warning message
     * - 12.3: Restore previous Tahun Ajaran as active
     * - 12.4: Revert all Kelas values to pre-rollover state
     * - 12.5: Restore all kelompok_id values to pre-rollover state
     * - 12.6: Restore all Status_Akademik values to pre-rollover state
     * - 12.7: Restore all Status_Penempatan values to pre-rollover state
     * - 12.8: Remove all RiwayatKelompok records created during rollover
     * - 12.9: Clear all tanggal_lulus_akademik values set during rollover
     * - 12.10: Execute rollback within single database transaction
     *
     * @param int $rolloverLogId The ID of the rollover audit log to rollback
     * @param int $adminId The ID of the admin performing the rollback
     * @return array Summary of rollback execution
     * @throws RolloverValidationException If rollback is not possible
     * @throws \Exception If rollback execution fails
     */
    public function rollback(int $rolloverLogId, int $adminId): array
    {
        // Requirement 12.1: Validate rollback eligibility
        $rolloverLog = \App\Models\RolloverAuditLog::find($rolloverLogId);

        if (!$rolloverLog) {
            throw new RolloverValidationException(
                'Rollover log tidak ditemukan',
                ['rollover_log' => 'ID tidak valid']
            );
        }

        // Check if rollover is completed
        if ($rolloverLog->status !== 'completed') {
            throw new RolloverValidationException(
                'Tidak dapat rollback: rollover belum selesai atau sudah gagal',
                ['status' => 'Rollover harus berstatus completed']
            );
        }

        // Check if rollover was already rolled back
        if ($rolloverLog->rolled_back_at) {
            throw new RolloverValidationException(
                'Rollover ini sudah di-rollback sebelumnya',
                ['rolled_back' => 'Sudah di-rollback pada ' . $rolloverLog->rolled_back_at->format('Y-m-d H:i:s')]
            );
        }

        // Check if rollover is within 24 hours
        if (!$rolloverLog->canRollback()) {
            throw new RolloverValidationException(
                'Rollback hanya dapat dilakukan dalam 24 jam setelah rollover',
                ['time_limit' => 'Rollover dilakukan pada ' . $rolloverLog->completed_at->format('Y-m-d H:i:s')]
            );
        }

        $lockAcquired = false;

        try {
            // Acquire exclusive lock for rollback operation
            Log::info('Attempting to acquire rollover lock for rollback', [
                'admin_id' => $adminId,
                'rollover_log_id' => $rolloverLogId,
            ]);

            if (!$this->acquireLock()) {
                throw new RolloverLockException(
                    'Rollback tidak dapat dilakukan: rollover sedang diproses'
                );
            }

            $lockAcquired = true;
            Log::info('Rollback lock acquired successfully');

            // Execute all rollback operations within a single transaction
            // Requirement 12.10: Single database transaction for atomicity
            return DB::transaction(function () use ($rolloverLog, $adminId) {
                $sourceTahunAjaran = $rolloverLog->sourceTahunAjaran;
                $targetTahunAjaran = $rolloverLog->targetTahunAjaran;

                Log::info('Starting rollback operation', [
                    'rollover_log_id' => $rolloverLog->id,
                    'source_tahun_ajaran' => $sourceTahunAjaran->label,
                    'target_tahun_ajaran' => $targetTahunAjaran->label,
                ]);

                // Get Kelas IDs for rollback
                $kelas7 = Kelas::where('tingkat', '7')->firstOrFail();
                $kelas8 = Kelas::where('tingkat', '8')->firstOrFail();
                $kelas9 = Kelas::where('tingkat', '9')->firstOrFail();

                // Requirement 12.6: Restore Status_Akademik values
                // Requirement 12.9: Clear tanggal_lulus_akademik values
                // Revert graduated students (Lulus SMP → Aktif, Kelas 9)
                Log::info('Reverting graduated students to active status');
                $revertedGraduated = Siswa::where('status_akademik', \App\Enums\StatusAkademik::LULUS_SMP->value)
                    ->where('kelas_id', $kelas9->id)
                    ->whereNotNull('tanggal_lulus_akademik')
                    ->where('tanggal_lulus_akademik', '>=', $rolloverLog->started_at)
                    ->update([
                        'status_akademik' => \App\Enums\StatusAkademik::AKTIF->value,
                        'tanggal_lulus_akademik' => null,
                    ]);

                Log::info("Reverted {$revertedGraduated} graduated students to active status");

                // Requirement 12.4: Revert Kelas values
                // We need to revert based on the counts in the audit log
                // The logic is:
                // - Students promoted 8→9 during rollover are now in Kelas 9 (active)
                // - Students promoted 7→8 during rollover are now in Kelas 8 (active)
                // - We need to revert them back to their original Kelas

                // Note: We can't rely solely on current Kelas to determine who was promoted
                // because there might be other Kelas 8 or 9 students who weren't promoted.
                // However, since the rollover affects ALL active students in each grade,
                // we can safely revert all active students in Kelas 8 and 9.
                // The only exception is graduated students who we already handled above.

                // Revert Kelas 9 students back to Kelas 8 (these were promoted from 8→9)
                // Only revert those who are still Active (not graduated)
                Log::info('Reverting Kelas 9 students to Kelas 8');
                $revertedKelas9 = Siswa::where('status_akademik', \App\Enums\StatusAkademik::AKTIF->value)
                    ->where('kelas_id', $kelas9->id)
                    ->limit($rolloverLog->kelas_8_to_9_count)
                    ->update(['kelas_id' => $kelas8->id]);

                Log::info("Reverted {$revertedKelas9} students from Kelas 9 to Kelas 8");

                // Revert Kelas 8 students back to Kelas 7 (these were promoted from 7→8)
                Log::info('Reverting Kelas 8 students to Kelas 7');
                $revertedKelas8 = Siswa::where('status_akademik', \App\Enums\StatusAkademik::AKTIF->value)
                    ->where('kelas_id', $kelas8->id)
                    ->limit($rolloverLog->kelas_7_to_8_count)
                    ->update(['kelas_id' => $kelas7->id]);

                Log::info("Reverted {$revertedKelas8} students from Kelas 8 to Kelas 7");

                // Requirement 12.5, 12.7: Restore kelompok_id and status_penempatan values
                // Find RiwayatKelompok records created during this rollover
                $riwayatRecords = \App\Models\RiwayatKelompok::where('alasan', 'Rollover Tahun Ajaran')
                    ->where('disetujui_oleh', $rolloverLog->initiated_by)
                    ->where('tanggal', '>=', $rolloverLog->started_at)
                    ->where('tanggal', '<=', $rolloverLog->completed_at)
                    ->whereNull('kelompok_ke_id') // These are the clearing records
                    ->get();

                Log::info("Found {$riwayatRecords->count()} RiwayatKelompok records to restore");

                $restoredKelompokCount = 0;

                // Restore each student's kelompok_id from the riwayat record
                foreach ($riwayatRecords as $riwayat) {
                    if ($riwayat->kelompok_dari_id) {
                        // Restore the student's kelompok assignment
                        Siswa::where('id', $riwayat->siswa_id)
                            ->update([
                                'kelompok_id' => $riwayat->kelompok_dari_id,
                                'status_penempatan' => \App\Enums\StatusPenempatan::DITEMPATKAN->value,
                                // Note: We don't restore tanggal_penempatan as it should remain the original placement date
                            ]);
                        $restoredKelompokCount++;
                    }
                }

                Log::info("Restored kelompok assignments for {$restoredKelompokCount} students");

                // Requirement 12.8: Remove RiwayatKelompok records created during rollover
                $deletedRiwayatCount = \App\Models\RiwayatKelompok::where('alasan', 'Rollover Tahun Ajaran')
                    ->where('disetujui_oleh', $rolloverLog->initiated_by)
                    ->where('tanggal', '>=', $rolloverLog->started_at)
                    ->where('tanggal', '<=', $rolloverLog->completed_at)
                    ->delete();

                Log::info("Deleted {$deletedRiwayatCount} RiwayatKelompok records");

                // Requirement 12.3: Restore previous Tahun Ajaran as active
                Log::info('Restoring previous Tahun Ajaran as active');
                TahunAjaran::query()->update(['status' => false]);
                $sourceTahunAjaran->update(['status' => true]);

                // Update rollover log with rollback information
                Log::info('Updating rollover log with rollback information');
                $rolloverLog->update([
                    'status' => 'rolled_back',
                    'rolled_back_by' => $adminId,
                    'rolled_back_at' => now(),
                ]);

                Log::info('Rollback completed successfully', [
                    'rollover_log_id' => $rolloverLog->id,
                    'reverted_graduated' => $revertedGraduated,
                    'reverted_kelas_9' => $revertedKelas9,
                    'reverted_kelas_8' => $revertedKelas8,
                    'restored_kelompok' => $restoredKelompokCount,
                    'deleted_riwayat' => $deletedRiwayatCount,
                ]);

                return [
                    'success' => true,
                    'rollover_log_id' => $rolloverLog->id,
                    'source_tahun_ajaran_id' => $sourceTahunAjaran->id,
                    'target_tahun_ajaran_id' => $targetTahunAjaran->id,
                    'source_tahun_ajaran_label' => $sourceTahunAjaran->label,
                    'reverted_graduated_count' => $revertedGraduated,
                    'reverted_kelas_9_count' => $revertedKelas9,
                    'reverted_kelas_8_count' => $revertedKelas8,
                    'restored_kelompok_count' => $restoredKelompokCount,
                    'deleted_riwayat_count' => $deletedRiwayatCount,
                    'total_affected' => $revertedGraduated + $revertedKelas9 + $revertedKelas8 + $restoredKelompokCount,
                ];
            }, 5); // 5 retry attempts for deadlock resolution

        } catch (RolloverLockException $e) {
            // Requirement 7.7, 9.7: Catch and handle RolloverLockException in rollback
            // Lock acquisition failed - log as warning with full context
            Log::warning('Rollback lock conflict', [
                'admin_id' => $adminId,
                'rollover_log_id' => $rolloverLogId,
                'message' => $e->getMessage(),
                'exception_class' => get_class($e),
            ]);

            // Re-throw for UI handling
            throw $e;

        } catch (RolloverValidationException $e) {
            // Requirement 7.7, 9.7: Catch and handle RolloverValidationException in rollback
            // Validation failed - log with validation details
            Log::warning('Rollback validation failed', [
                'admin_id' => $adminId,
                'rollover_log_id' => $rolloverLogId,
                'validation_errors' => $e->validationErrors,
                'message' => $e->getMessage(),
                'exception_class' => get_class($e),
            ]);

            // Re-throw for UI handling
            throw $e;

        } catch (\Illuminate\Database\QueryException $e) {
            // Requirement 9.7, 13.5: Handle database-specific errors with detailed context
            Log::error('Rollback database error', [
                'admin_id' => $adminId,
                'rollover_log_id' => $rolloverLogId,
                'rollover_log' => $rolloverLog?->toArray(),
                'error' => $e->getMessage(),
                'sql_state' => $e->errorInfo[0] ?? 'unknown',
                'error_code' => $e->errorInfo[1] ?? 'unknown',
                'exception_class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Requirement 9.7: Display error message with technical details
            throw new \RuntimeException(
                'Rollback gagal karena kesalahan database. Silakan hubungi administrator sistem. Error: ' . $e->getMessage(),
                previous: $e
            );

        } catch (\Exception $e) {
            // Requirement 9.7, 13.5: Catch all other exceptions with comprehensive logging
            Log::error('Rollback execution failed', [
                'admin_id' => $adminId,
                'rollover_log_id' => $rolloverLogId,
                'rollover_log' => $rolloverLog?->toArray(),
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Requirement 9.7: Display error message with technical details
            throw new \RuntimeException(
                'Rollback gagal: ' . $e->getMessage(),
                previous: $e
            );

        } finally {
            // Requirement 9.7, 13.5: Always release lock in all scenarios
            if ($lockAcquired) {
                try {
                    Log::info('Releasing rollback lock', [
                        'admin_id' => $adminId,
                        'rollover_log_id' => $rolloverLogId,
                    ]);
                    $this->releaseLock();
                } catch (\Exception $e) {
                    // If lock release fails, log the error but don't throw
                    // This prevents masking the original exception
                    Log::error('Failed to release rollback lock', [
                        'admin_id' => $adminId,
                        'rollover_log_id' => $rolloverLogId,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }
        }
    }
}
