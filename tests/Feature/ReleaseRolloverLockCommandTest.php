<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

/**
 * Test suite for ReleaseRolloverLock Artisan command
 *
 * Tests the command's ability to:
 * - Detect lock status
 * - Release held locks
 * - Handle cases where no lock is held
 * - Require confirmation (or skip with --force)
 * - Log manual lock releases
 *
 * **Validates: Requirements 20.5**
 */
class ReleaseRolloverLockCommandTest extends TestCase
{
    /**
     * Lock name used for MySQL advisory locking
     * Must match the lock name in the command and service
     */
    private const LOCK_NAME = 'rollover_operation';

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure no lock is held at the start of each test
        $this->releaseLock();
    }

    protected function tearDown(): void
    {
        // Clean up any locks after each test
        $this->releaseLock();

        parent::tearDown();
    }

    /**
     * Test command executes successfully when no lock is held
     */
    public function test_command_reports_no_lock_when_lock_is_free(): void
    {
        $this->artisan('rollover:release-lock --force')
            ->expectsOutput('✅ No lock is currently held. No action needed.')
            ->assertSuccessful();
    }

    /**
     * Test command can detect and release a held lock with --force flag
     */
    public function test_command_releases_lock_when_lock_is_held_with_force(): void
    {
        // Acquire the lock
        $this->acquireLock();

        // Verify lock is held
        $this->assertTrue($this->isLockHeld());

        // Release the lock with --force
        $this->artisan('rollover:release-lock --force')
            ->expectsOutput('Releasing rollover lock...')
            ->expectsOutput('✅ Lock released successfully!')
            ->assertSuccessful();

        // Verify lock is no longer held
        $this->assertFalse($this->isLockHeld());
    }

    /**
     * Test command requires confirmation when --force is not used
     */
    public function test_command_requires_confirmation_without_force_flag(): void
    {
        // Acquire the lock
        $this->acquireLock();

        // Run command without --force and decline confirmation
        $this->artisan('rollover:release-lock')
            ->expectsConfirmation(
                'Are you sure you want to release the lock? This could interfere with a running rollover operation.',
                'no'
            )
            ->expectsOutput('Operation cancelled.')
            ->assertSuccessful();

        // Verify lock is still held
        $this->assertTrue($this->isLockHeld());

        // Clean up
        $this->releaseLock();
    }

    /**
     * Test command releases lock when confirmation is accepted
     */
    public function test_command_releases_lock_when_confirmation_accepted(): void
    {
        // Acquire the lock
        $this->acquireLock();

        // Run command without --force but accept confirmation
        $this->artisan('rollover:release-lock')
            ->expectsConfirmation(
                'Are you sure you want to release the lock? This could interfere with a running rollover operation.',
                'yes'
            )
            ->expectsOutput('Releasing rollover lock...')
            ->expectsOutput('✅ Lock released successfully!')
            ->assertSuccessful();

        // Verify lock is no longer held
        $this->assertFalse($this->isLockHeld());
    }

    /**
     * Test command logs manual lock releases
     */
    public function test_command_logs_manual_lock_release(): void
    {
        // Set up log expectations
        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function ($message, $context) {
                return $message === 'Rollover lock manually released via Artisan command'
                    && $context['command'] === 'rollover:release-lock'
                    && isset($context['timestamp']);
            });

        // Acquire the lock
        $this->acquireLock();

        // Release the lock with --force
        $this->artisan('rollover:release-lock --force')
            ->assertSuccessful();
    }

    /**
     * Test command displays warning message about emergency use
     */
    public function test_command_displays_warning_about_emergency_use(): void
    {
        $this->artisan('rollover:release-lock --force')
            ->expectsOutput('⚠️  WARNING: This command should only be used in emergency situations!')
            ->assertSuccessful();
    }

    /**
     * Test command displays lock status when lock is held
     */
    public function test_command_displays_lock_status_when_lock_is_held(): void
    {
        // Acquire the lock
        $this->acquireLock();

        // Get the connection ID that holds the lock
        $connectionId = $this->getLockConnectionId();

        $this->artisan('rollover:release-lock --force')
            ->expectsOutput("🔒 Lock is currently held by MySQL connection ID: {$connectionId}")
            ->assertSuccessful();
    }

    /**
     * Test command handles errors gracefully
     */
    public function test_command_handles_errors_gracefully(): void
    {
        // Mock DB to throw an exception
        DB::shouldReceive('selectOne')
            ->andThrow(new \Exception('Database connection error'));

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message, $context) {
                return $message === 'Failed to release rollover lock via Artisan command'
                    && isset($context['error']);
            });

        $this->artisan('rollover:release-lock --force')
            ->expectsOutput('❌ Error releasing lock: Database connection error')
            ->assertFailed();
    }

    /**
     * Helper: Acquire the rollover lock
     */
    private function acquireLock(): bool
    {
        $result = DB::selectOne(
            'SELECT GET_LOCK(?, ?) as locked',
            [self::LOCK_NAME, 300]
        );

        return $result->locked === 1;
    }

    /**
     * Helper: Release the rollover lock
     */
    private function releaseLock(): void
    {
        DB::select('SELECT RELEASE_LOCK(?)', [self::LOCK_NAME]);
    }

    /**
     * Helper: Check if the rollover lock is currently held
     */
    private function isLockHeld(): bool
    {
        $result = DB::selectOne(
            'SELECT IS_USED_LOCK(?) as connection_id',
            [self::LOCK_NAME]
        );

        return $result->connection_id !== null;
    }

    /**
     * Helper: Get the connection ID that holds the lock
     */
    private function getLockConnectionId(): ?int
    {
        $result = DB::selectOne(
            'SELECT IS_USED_LOCK(?) as connection_id',
            [self::LOCK_NAME]
        );

        return $result->connection_id;
    }
}
