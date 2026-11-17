<?php

namespace Tests\Unit\Services;

use App\Services\IntegratedRolloverService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Unit Tests for IntegratedRolloverService Database Locking Mechanism
 *
 * Tests the MySQL advisory locking functionality that prevents concurrent rollover operations.
 * Covers:
 * - acquireLock() - MySQL GET_LOCK()
 * - releaseLock() - MySQL RELEASE_LOCK()
 * - isRolloverInProgress() - MySQL IS_USED_LOCK()
 *
 * Requirements tested: 20.1, 20.2, 20.3, 20.4, 20.5, 20.6
 *
 * Note: Database-dependent tests require MySQL. When running on SQLite (default for testing),
 * only constants and method accessibility are verified.
 */
class IntegratedRolloverServiceLockingTest extends TestCase
{
    use RefreshDatabase;

    protected IntegratedRolloverService $service;
    protected bool $isMySql;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(IntegratedRolloverService::class);

        // Check if we're using MySQL
        $this->isMySql = DB::connection()->getDriverName() === 'mysql';

        // Only release locks if using MySQL
        if ($this->isMySql) {
            $this->releaseLockDirectly();
        }
    }

    protected function tearDown(): void
    {
        // Clean up any locks after each test (MySQL only)
        if ($this->isMySql) {
            $this->releaseLockDirectly();
        }
        parent::tearDown();
    }

    /**
     * Helper method to directly release the rollover lock
     * This is used in setUp/tearDown to ensure clean state
     */
    private function releaseLockDirectly(): void
    {
        try {
            DB::select('SELECT RELEASE_LOCK(?)', ['rollover_operation']);
        } catch (\Exception $e) {
            // Ignore errors in tearDown
        }
    }

    /**
     * Helper method to directly acquire the rollover lock
     * This simulates another process holding the lock
     *
     * @param int $timeout Lock timeout in seconds
     * @return bool True if lock was acquired
     */
    private function acquireLockDirectly(int $timeout = 300): bool
    {
        if (!$this->isMySql) {
            return true; // Simulate success on non-MySQL
        }

        $result = DB::selectOne(
            'SELECT GET_LOCK(?, ?) as locked',
            ['rollover_operation', $timeout]
        );
        return $result->locked === 1;
    }

    /**
     * Helper method to check if lock is currently held
     *
     * @return bool True if lock is held by any connection
     */
    private function isLockHeld(): bool
    {
        if (!$this->isMySql) {
            return false; // Simulate no lock on non-MySQL
        }

        $result = DB::selectOne(
            'SELECT IS_USED_LOCK(?) as connection_id',
            ['rollover_operation']
        );
        return $result->connection_id !== null;
    }

    // ========================================================================
    // Requirement 20.1, 20.5: Test lock constants
    // ========================================================================

    public function test_lock_has_300_second_timeout()
    {
        // This test verifies the LOCK_TIMEOUT constant is set correctly
        // We can't easily test the actual timeout without waiting 5 minutes,
        // but we can verify the constant exists and has the right value

        $reflection = new \ReflectionClass($this->service);
        $lockTimeoutConstant = $reflection->getConstant('LOCK_TIMEOUT');

        // Assert
        $this->assertEquals(300, $lockTimeoutConstant, 'LOCK_TIMEOUT should be 300 seconds (5 minutes)');
    }

    public function test_lock_name_constant_is_correct()
    {
        // Verify the lock name constant is correctly set
        $reflection = new \ReflectionClass($this->service);
        $lockNameConstant = $reflection->getConstant('LOCK_NAME');

        // Assert
        $this->assertEquals('rollover_operation', $lockNameConstant, 'LOCK_NAME should be "rollover_operation"');
    }

    // ========================================================================
    // MySQL-specific tests (skipped on SQLite)
    // ========================================================================

    public function test_lock_can_be_acquired_when_no_lock_exists()
    {
        if (!$this->isMySql) {
            $this->markTestSkipped('This test requires MySQL database');
        }

        // Arrange - Ensure no lock is held
        $this->assertFalse($this->service->isRolloverInProgress());

        // Act - Use reflection to access private acquireLock method
        $reflection = new \ReflectionClass($this->service);
        $acquireLockMethod = $reflection->getMethod('acquireLock');
        $acquireLockMethod->setAccessible(true);

        $result = $acquireLockMethod->invoke($this->service);

        // Assert
        $this->assertTrue($result, 'Lock acquisition should succeed when no lock exists');
        $this->assertTrue($this->isLockHeld(), 'Lock should be held after acquisition');
    }

    public function test_lock_cannot_be_acquired_when_lock_already_held()
    {
        if (!$this->isMySql) {
            $this->markTestSkipped('This test requires MySQL database');
        }

        // Arrange - Acquire lock directly to simulate another process
        $this->assertTrue($this->acquireLockDirectly());
        $this->assertTrue($this->isLockHeld());

        // Act - Try to acquire lock through service
        $reflection = new \ReflectionClass($this->service);
        $acquireLockMethod = $reflection->getMethod('acquireLock');
        $acquireLockMethod->setAccessible(true);

        $result = $acquireLockMethod->invoke($this->service);

        // Assert
        $this->assertFalse($result, 'Lock acquisition should fail when lock is already held');
    }

    public function test_is_rollover_in_progress_returns_false_when_no_lock()
    {
        if (!$this->isMySql) {
            $this->markTestSkipped('This test requires MySQL database');
        }

        // Arrange - Ensure no lock is held
        $this->releaseLockDirectly();

        // Act
        $result = $this->service->isRolloverInProgress();

        // Assert
        $this->assertFalse($result, 'isRolloverInProgress should return false when no lock is held');
    }

    public function test_is_rollover_in_progress_returns_true_when_lock_held()
    {
        if (!$this->isMySql) {
            $this->markTestSkipped('This test requires MySQL database');
        }

        // Arrange - Acquire lock to simulate ongoing rollover
        $this->assertTrue($this->acquireLockDirectly());

        // Act
        $result = $this->service->isRolloverInProgress();

        // Assert
        $this->assertTrue($result, 'isRolloverInProgress should return true when lock is held');
    }

    public function test_lock_can_be_released_after_acquisition()
    {
        if (!$this->isMySql) {
            $this->markTestSkipped('This test requires MySQL database');
        }

        // Arrange - Acquire lock first
        $reflection = new \ReflectionClass($this->service);
        $acquireLockMethod = $reflection->getMethod('acquireLock');
        $acquireLockMethod->setAccessible(true);
        $acquireLockMethod->invoke($this->service);

        $this->assertTrue($this->isLockHeld());

        // Act - Release lock
        $releaseLockMethod = $reflection->getMethod('releaseLock');
        $releaseLockMethod->setAccessible(true);
        $releaseLockMethod->invoke($this->service);

        // Assert
        $this->assertFalse($this->isLockHeld(), 'Lock should be released after releaseLock() is called');
        $this->assertFalse($this->service->isRolloverInProgress());
    }

    public function test_release_lock_is_safe_when_no_lock_held()
    {
        if (!$this->isMySql) {
            $this->markTestSkipped('This test requires MySQL database');
        }

        // Arrange - Ensure no lock is held
        $this->releaseLockDirectly();
        $this->assertFalse($this->isLockHeld());

        // Act - Try to release non-existent lock
        $reflection = new \ReflectionClass($this->service);
        $releaseLockMethod = $reflection->getMethod('releaseLock');
        $releaseLockMethod->setAccessible(true);
        $releaseLockMethod->invoke($this->service);

        // Assert - Should not throw exception
        $this->assertFalse($this->isLockHeld());
        $this->assertTrue(true, 'Releasing non-existent lock should not cause errors');
    }

    public function test_complete_lock_acquire_release_cycle()
    {
        if (!$this->isMySql) {
            $this->markTestSkipped('This test requires MySQL database');
        }

        // Arrange
        $reflection = new \ReflectionClass($this->service);
        $acquireLockMethod = $reflection->getMethod('acquireLock');
        $acquireLockMethod->setAccessible(true);
        $releaseLockMethod = $reflection->getMethod('releaseLock');
        $releaseLockMethod->setAccessible(true);

        // Act & Assert - Step 1: No lock initially
        $this->assertFalse($this->service->isRolloverInProgress());

        // Act & Assert - Step 2: Acquire lock
        $acquired = $acquireLockMethod->invoke($this->service);
        $this->assertTrue($acquired);
        $this->assertTrue($this->service->isRolloverInProgress());

        // Act & Assert - Step 3: Cannot acquire second lock
        $secondAcquire = $acquireLockMethod->invoke($this->service);
        $this->assertFalse($secondAcquire);

        // Act & Assert - Step 4: Release lock
        $releaseLockMethod->invoke($this->service);
        $this->assertFalse($this->service->isRolloverInProgress());

        // Act & Assert - Step 5: Can acquire again after release
        $thirdAcquire = $acquireLockMethod->invoke($this->service);
        $this->assertTrue($thirdAcquire);
        $this->assertTrue($this->service->isRolloverInProgress());
    }

    public function test_lock_prevents_concurrent_operations()
    {
        if (!$this->isMySql) {
            $this->markTestSkipped('This test requires MySQL database');
        }

        // Simulate first admin starting rollover
        $this->assertTrue($this->acquireLockDirectly(), 'First admin should acquire lock');
        $this->assertTrue($this->service->isRolloverInProgress());

        // Simulate second admin trying to start rollover
        $reflection = new \ReflectionClass($this->service);
        $acquireLockMethod = $reflection->getMethod('acquireLock');
        $acquireLockMethod->setAccessible(true);

        $secondAttempt = $acquireLockMethod->invoke($this->service);

        // Assert
        $this->assertFalse($secondAttempt, 'Second admin should NOT be able to acquire lock');
        $this->assertTrue($this->service->isRolloverInProgress(), 'Lock should still be held by first admin');
    }

    public function test_lock_allows_operation_after_previous_completion()
    {
        if (!$this->isMySql) {
            $this->markTestSkipped('This test requires MySQL database');
        }

        // Simulate complete first rollover
        $this->assertTrue($this->acquireLockDirectly());
        $this->assertTrue($this->service->isRolloverInProgress());
        $this->releaseLockDirectly();
        $this->assertFalse($this->service->isRolloverInProgress());

        // Simulate second rollover starting
        $reflection = new \ReflectionClass($this->service);
        $acquireLockMethod = $reflection->getMethod('acquireLock');
        $acquireLockMethod->setAccessible(true);

        $secondRollover = $acquireLockMethod->invoke($this->service);

        // Assert
        $this->assertTrue($secondRollover, 'Second rollover should be allowed after first completes');
        $this->assertTrue($this->service->isRolloverInProgress());
    }

    public function test_multiple_release_calls_are_safe()
    {
        if (!$this->isMySql) {
            $this->markTestSkipped('This test requires MySQL database');
        }

        // Arrange - Acquire lock
        $reflection = new \ReflectionClass($this->service);
        $acquireLockMethod = $reflection->getMethod('acquireLock');
        $acquireLockMethod->setAccessible(true);
        $releaseLockMethod = $reflection->getMethod('releaseLock');
        $releaseLockMethod->setAccessible(true);

        $acquireLockMethod->invoke($this->service);
        $this->assertTrue($this->isLockHeld());

        // Act - Release multiple times
        $releaseLockMethod->invoke($this->service);
        $releaseLockMethod->invoke($this->service);
        $releaseLockMethod->invoke($this->service);

        // Assert - Should not throw exceptions
        $this->assertFalse($this->isLockHeld());
        $this->assertTrue(true, 'Multiple release calls should be safe');
    }

    public function test_is_rollover_in_progress_does_not_modify_lock_state()
    {
        if (!$this->isMySql) {
            $this->markTestSkipped('This test requires MySQL database');
        }

        // Arrange - Acquire lock
        $this->assertTrue($this->acquireLockDirectly());
        $this->assertTrue($this->isLockHeld());

        // Act - Call isRolloverInProgress multiple times
        $result1 = $this->service->isRolloverInProgress();
        $result2 = $this->service->isRolloverInProgress();
        $result3 = $this->service->isRolloverInProgress();

        // Assert
        $this->assertTrue($result1);
        $this->assertTrue($result2);
        $this->assertTrue($result3);
        $this->assertTrue($this->isLockHeld(), 'Checking lock status should not release the lock');
    }
}
