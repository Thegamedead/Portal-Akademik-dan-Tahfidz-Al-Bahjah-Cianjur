<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Release the rollover operation advisory lock
 *
 * This command manually releases the MySQL advisory lock used by the
 * IntegratedRolloverService. It should only be used in emergency situations
 * where a rollover process has terminated abnormally and left the lock held.
 *
 * Requirements addressed:
 * - 20.5: Manual lock release for abnormal process termination
 */
class ReleaseRolloverLock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rollover:release-lock
                            {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually release the rollover operation advisory lock (use only in emergency situations)';

    /**
     * Lock name used for MySQL advisory locking
     * Must match the lock name in IntegratedRolloverService
     */
    private const LOCK_NAME = 'rollover_operation';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->warn('⚠️  WARNING: This command should only be used in emergency situations!');
        $this->newLine();
        $this->line('Use this command when:');
        $this->line('  - A rollover process terminated abnormally');
        $this->line('  - The lock is preventing new rollover operations');
        $this->line('  - You have verified no rollover is currently running');
        $this->newLine();

        // Check if lock is currently held
        $lockStatus = $this->checkLockStatus();

        if ($lockStatus === null) {
            $this->info('✅ No lock is currently held. No action needed.');
            return Command::SUCCESS;
        }

        // Display lock information
        $this->warn("🔒 Lock is currently held by MySQL connection ID: {$lockStatus}");
        $this->newLine();

        // Require confirmation unless --force is used
        if (!$this->option('force')) {
            $confirmed = $this->confirm(
                'Are you sure you want to release the lock? This could interfere with a running rollover operation.',
                false
            );

            if (!$confirmed) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }
        }

        // Release the lock
        $this->info('Releasing rollover lock...');

        try {
            $released = $this->releaseLock();

            if ($released) {
                $this->info('✅ Lock released successfully!');

                // Log the manual lock release
                Log::warning('Rollover lock manually released via Artisan command', [
                    'command' => 'rollover:release-lock',
                    'user' => get_current_user(),
                    'timestamp' => now()->toDateTimeString(),
                    'previous_connection_id' => $lockStatus,
                ]);

                return Command::SUCCESS;
            } else {
                $this->error('❌ Failed to release lock. The lock may have already been released or expired.');
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error("❌ Error releasing lock: {$e->getMessage()}");

            Log::error('Failed to release rollover lock via Artisan command', [
                'command' => 'rollover:release-lock',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }

    /**
     * Check if the rollover lock is currently held
     *
     * @return int|null Connection ID if lock is held, null if lock is free
     */
    private function checkLockStatus(): ?int
    {
        $result = DB::selectOne(
            'SELECT IS_USED_LOCK(?) as connection_id',
            [self::LOCK_NAME]
        );

        return $result->connection_id;
    }

    /**
     * Release the rollover operation lock
     *
     * @return bool True if lock was released, false if lock was not held
     */
    private function releaseLock(): bool
    {
        $result = DB::selectOne(
            'SELECT RELEASE_LOCK(?) as released',
            [self::LOCK_NAME]
        );

        // RELEASE_LOCK returns 1 if successful, 0 if lock was not held, NULL on error
        return $result->released === 1;
    }
}
