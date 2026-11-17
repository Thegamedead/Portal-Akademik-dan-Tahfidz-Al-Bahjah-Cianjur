<?php

namespace App\Services\Handlers;

use App\Models\RolloverAuditLog;
use App\DataTransferObjects\ProgressionResult;
use App\DataTransferObjects\TahfidzClearResult;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Rollover Audit Logger
 *
 * Handles audit logging for all integrated year-end rollover operations.
 * Tracks:
 * - Rollover initiation with source and target academic years
 * - Successful completion with impact metrics
 * - Failure conditions with error details and stack traces
 * - Rollback operations
 */
class RolloverAuditLogger
{
    /**
     * Create initial audit log entry when rollover starts
     *
     * Creates a log record with status 'in_progress' to track the start
     * of the rollover operation. This entry will be updated with results
     * when the operation completes or fails.
     *
     * @param int $adminId Admin initiating rollover
     * @param int $sourceTahunAjaranId Current active Tahun Ajaran
     * @param int $targetTahunAjaranId Target Tahun Ajaran
     * @return RolloverAuditLog The created log entry
     */
    public function createInitialLog(
        int $adminId,
        int $sourceTahunAjaranId,
        int $targetTahunAjaranId
    ): RolloverAuditLog {
        $log = RolloverAuditLog::create([
            'initiated_by' => $adminId,
            'source_tahun_ajaran_id' => $sourceTahunAjaranId,
            'target_tahun_ajaran_id' => $targetTahunAjaranId,
            'kelas_7_to_8_count' => 0,
            'kelas_8_to_9_count' => 0,
            'graduated_count' => 0,
            'kelompok_cleared_count' => 0,
            'status' => 'in_progress',
            'started_at' => Carbon::now(),
        ]);

        Log::info('Rollover audit log created', [
            'log_id' => $log->id,
            'admin_id' => $adminId,
            'source_tahun_ajaran_id' => $sourceTahunAjaranId,
            'target_tahun_ajaran_id' => $targetTahunAjaranId,
        ]);

        return $log;
    }

    /**
     * Update audit log with successful completion data
     *
     * Marks the rollover as completed and records all impact metrics
     * from the academic progression and Tahfidz clearing operations.
     *
     * @param RolloverAuditLog $log The log entry to update
     * @param ProgressionResult $academicResult Academic progression results
     * @param TahfidzClearResult $tahfidzResult Tahfidz clearing results
     * @return void
     */
    public function markCompleted(
        RolloverAuditLog $log,
        ProgressionResult $academicResult,
        TahfidzClearResult $tahfidzResult
    ): void {
        $log->update([
            'kelas_7_to_8_count' => $academicResult->kelas7To8Count,
            'kelas_8_to_9_count' => $academicResult->kelas8To9Count,
            'graduated_count' => $academicResult->graduatedCount,
            'kelompok_cleared_count' => $tahfidzResult->clearedCount,
            'status' => 'completed',
            'completed_at' => Carbon::now(),
        ]);

        Log::info('Rollover completed successfully', [
            'log_id' => $log->id,
            'kelas_7_to_8' => $academicResult->kelas7To8Count,
            'kelas_8_to_9' => $academicResult->kelas8To9Count,
            'graduated' => $academicResult->graduatedCount,
            'kelompok_cleared' => $tahfidzResult->clearedCount,
            'total_affected' => $log->totalAffected,
        ]);
    }

    /**
     * Update audit log with failure information
     *
     * Records error details including the exception message and full stack trace
     * for troubleshooting and compliance. Sets the status to 'failed' and records
     * the completion timestamp.
     *
     * @param RolloverAuditLog $log The log entry to update
     * @param \Exception $exception The exception that caused failure
     * @return void
     */
    public function markFailed(RolloverAuditLog $log, \Exception $exception): void
    {
        $log->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
            'error_trace' => $exception->getTraceAsString(),
            'completed_at' => Carbon::now(),
        ]);

        Log::error('Rollover failed', [
            'log_id' => $log->id,
            'error' => $exception->getMessage(),
            'exception_class' => get_class($exception),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    /**
     * Record rollback operation
     *
     * Updates the original rollover log to indicate it has been rolled back.
     * Records the admin who performed the rollback and the timestamp.
     *
     * @param RolloverAuditLog $log The original rollover log
     * @param int $adminId Admin performing rollback
     * @return void
     */
    public function recordRollback(RolloverAuditLog $log, int $adminId): void
    {
        $log->update([
            'status' => 'rolled_back',
            'rolled_back_by' => $adminId,
            'rolled_back_at' => Carbon::now(),
        ]);

        Log::warning('Rollover rolled back', [
            'log_id' => $log->id,
            'original_admin_id' => $log->initiated_by,
            'rollback_admin_id' => $adminId,
            'source_tahun_ajaran_id' => $log->source_tahun_ajaran_id,
            'target_tahun_ajaran_id' => $log->target_tahun_ajaran_id,
        ]);
    }
}
