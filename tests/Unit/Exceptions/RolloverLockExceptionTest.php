<?php

namespace Tests\Unit\Exceptions;

use App\Exceptions\RolloverLockException;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Unit tests for RolloverLockException
 *
 * Tests exception behavior for concurrent rollover operations
 * @covers \App\Exceptions\RolloverLockException
 */
class RolloverLockExceptionTest extends TestCase
{
    /**
     * Test that exception is created with default message
     */
    public function test_it_creates_exception_with_default_message(): void
    {
        // Execute
        $exception = new RolloverLockException();

        // Assert
        $this->assertInstanceOf(RolloverLockException::class, $exception);
        $this->assertEquals(
            'Rollover sedang diproses oleh admin lain, silakan tunggu',
            $exception->getMessage()
        );
        $this->assertEquals(409, $exception->getCode());
    }

    /**
     * Test that exception can be created with custom message
     */
    public function test_it_creates_exception_with_custom_message(): void
    {
        // Arrange
        $customMessage = 'Custom lock message for testing';

        // Execute
        $exception = new RolloverLockException($customMessage);

        // Assert
        $this->assertEquals($customMessage, $exception->getMessage());
        $this->assertEquals(409, $exception->getCode());
    }

    /**
     * Test that exception reports false (does not log to error tracking)
     */
    public function test_it_does_not_report_to_error_tracking(): void
    {
        // Arrange
        $exception = new RolloverLockException();

        // Execute
        $shouldReport = $exception->report();

        // Assert
        $this->assertFalse($shouldReport);
    }

    /**
     * Test that exception renders JSON response for API requests
     */
    public function test_it_renders_json_response_for_api_requests(): void
    {
        // Arrange
        $exception = new RolloverLockException();
        $request = Request::create('/api/rollover', 'POST');
        $request->headers->set('Accept', 'application/json');

        // Execute
        $response = $exception->render($request);

        // Assert
        $this->assertEquals(409, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals(
            'Rollover sedang diproses oleh admin lain, silakan tunggu',
            $data['message']
        );
        $this->assertEquals('rollover_lock_conflict', $data['error']);
    }

    /**
     * Test that exception renders redirect response for web requests
     */
    public function test_it_renders_redirect_response_for_web_requests(): void
    {
        // Arrange
        $exception = new RolloverLockException();
        $request = Request::create('/admin/rollover', 'POST');
        $request->headers->set('Referer', '/admin/dashboard');

        // Execute
        $response = $exception->render($request);

        // Assert
        $this->assertEquals(302, $response->getStatusCode());

        $session = $response->getSession();
        $this->assertTrue($session->has('errors'));

        $errors = $session->get('errors');
        $this->assertTrue($errors->has('error'));
        $this->assertEquals(
            'Rollover sedang diproses oleh admin lain, silakan tunggu',
            $errors->first('error')
        );
    }

    /**
     * Test exception HTTP status code is 409 Conflict
     */
    public function test_it_has_409_conflict_status_code(): void
    {
        // Arrange & Execute
        $exception = new RolloverLockException();

        // Assert
        $this->assertEquals(409, $exception->getCode());
    }

    /**
     * Test that exception can be thrown and caught
     */
    public function test_it_can_be_thrown_and_caught(): void
    {
        // Arrange
        $exceptionThrown = false;

        // Execute
        try {
            throw new RolloverLockException();
        } catch (RolloverLockException $e) {
            $exceptionThrown = true;
        }

        // Assert
        $this->assertTrue($exceptionThrown);
    }
}
