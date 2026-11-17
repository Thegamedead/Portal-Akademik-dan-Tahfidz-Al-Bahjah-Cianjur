<?php

namespace Tests\Unit\Exceptions;

use App\Exceptions\RolloverValidationException;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Unit tests for RolloverValidationException
 *
 * Tests exception behavior for rollover validation failures
 * @covers \App\Exceptions\RolloverValidationException
 */
class RolloverValidationExceptionTest extends TestCase
{
    /**
     * Test that exception is created with message and no validation errors
     */
    public function test_it_creates_exception_with_message_only(): void
    {
        // Arrange
        $message = 'Silakan pilih Tahun Ajaran tujuan';

        // Execute
        $exception = new RolloverValidationException($message);

        // Assert
        $this->assertInstanceOf(RolloverValidationException::class, $exception);
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals(422, $exception->getCode());
        $this->assertEmpty($exception->getValidationErrors());
    }

    /**
     * Test that exception is created with message and validation errors
     */
    public function test_it_creates_exception_with_validation_errors(): void
    {
        // Arrange
        $message = 'Validasi rollover gagal';
        $validationErrors = [
            'target_tahun_ajaran' => 'Tahun Ajaran tujuan tidak ditemukan',
            'active_students' => 'Tidak ada siswa aktif yang akan di-rollover'
        ];

        // Execute
        $exception = new RolloverValidationException($message, $validationErrors);

        // Assert
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals(422, $exception->getCode());
        $this->assertEquals($validationErrors, $exception->getValidationErrors());
        $this->assertCount(2, $exception->getValidationErrors());
    }

    /**
     * Test that exception reports false (does not log to error tracking)
     */
    public function test_it_does_not_report_to_error_tracking(): void
    {
        // Arrange
        $exception = new RolloverValidationException('Test validation error');

        // Execute
        $shouldReport = $exception->report();

        // Assert
        $this->assertFalse($shouldReport);
    }

    /**
     * Test that exception renders JSON response for API requests without validation errors
     */
    public function test_it_renders_json_response_without_validation_errors(): void
    {
        // Arrange
        $message = 'Tahun Ajaran tujuan sama dengan yang aktif';
        $exception = new RolloverValidationException($message);
        $request = Request::create('/api/rollover', 'POST');
        $request->headers->set('Accept', 'application/json');

        // Execute
        $response = $exception->render($request);

        // Assert
        $this->assertEquals(422, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('error', $data);
        $this->assertArrayNotHasKey('errors', $data);
        $this->assertEquals($message, $data['message']);
        $this->assertEquals('rollover_validation_failed', $data['error']);
    }

    /**
     * Test that exception renders JSON response for API requests with validation errors
     */
    public function test_it_renders_json_response_with_validation_errors(): void
    {
        // Arrange
        $message = 'Validasi gagal';
        $validationErrors = [
            'kelas' => 'Data kelas tidak lengkap',
            'students' => 'Tidak ada siswa untuk diproses'
        ];
        $exception = new RolloverValidationException($message, $validationErrors);
        $request = Request::create('/api/rollover', 'POST');
        $request->headers->set('Accept', 'application/json');

        // Execute
        $response = $exception->render($request);

        // Assert
        $this->assertEquals(422, $response->getStatusCode());

        $data = $response->getData(true);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('error', $data);
        $this->assertArrayHasKey('errors', $data);
        $this->assertEquals($message, $data['message']);
        $this->assertEquals('rollover_validation_failed', $data['error']);
        $this->assertEquals($validationErrors, $data['errors']);
    }

    /**
     * Test that exception renders redirect response for web requests without validation errors
     */
    public function test_it_renders_redirect_response_without_validation_errors(): void
    {
        // Arrange
        $message = 'Tidak ada siswa aktif yang akan di-rollover';
        $exception = new RolloverValidationException($message);
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
        $this->assertEquals($message, $errors->first('error'));
    }

    /**
     * Test that exception renders redirect response for web requests with validation errors
     */
    public function test_it_renders_redirect_response_with_validation_errors(): void
    {
        // Arrange
        $message = 'Data tidak valid';
        $validationErrors = [
            'kelas_7' => 'Kelas 7 tidak ditemukan',
            'kelas_8' => 'Kelas 8 tidak ditemukan'
        ];
        $exception = new RolloverValidationException($message, $validationErrors);
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
        $this->assertTrue($errors->has('kelas_7'));
        $this->assertTrue($errors->has('kelas_8'));
        $this->assertEquals($message, $errors->first('error'));
        $this->assertEquals('Kelas 7 tidak ditemukan', $errors->first('kelas_7'));
        $this->assertEquals('Kelas 8 tidak ditemukan', $errors->first('kelas_8'));
    }

    /**
     * Test exception HTTP status code is 422 Unprocessable Entity
     */
    public function test_it_has_422_unprocessable_entity_status_code(): void
    {
        // Arrange & Execute
        $exception = new RolloverValidationException('Test error');

        // Assert
        $this->assertEquals(422, $exception->getCode());
    }

    /**
     * Test that validation errors are readonly and accessible
     */
    public function test_it_provides_readonly_access_to_validation_errors(): void
    {
        // Arrange
        $validationErrors = [
            'field1' => 'Error 1',
            'field2' => 'Error 2'
        ];
        $exception = new RolloverValidationException('Test', $validationErrors);

        // Execute
        $retrievedErrors = $exception->getValidationErrors();

        // Assert
        $this->assertEquals($validationErrors, $retrievedErrors);
        $this->assertSame($retrievedErrors, $exception->validationErrors);
    }

    /**
     * Test that empty validation errors array is handled correctly
     */
    public function test_it_handles_empty_validation_errors_array(): void
    {
        // Arrange & Execute
        $exception = new RolloverValidationException('Test error', []);

        // Assert
        $this->assertIsArray($exception->getValidationErrors());
        $this->assertEmpty($exception->getValidationErrors());
    }

    /**
     * Test that exception can be thrown and caught
     */
    public function test_it_can_be_thrown_and_caught(): void
    {
        // Arrange
        $exceptionThrown = false;
        $message = 'Data tidak valid: Kelas kurang dari 7';

        // Execute
        try {
            throw new RolloverValidationException($message);
        } catch (RolloverValidationException $e) {
            $exceptionThrown = true;
            $caughtMessage = $e->getMessage();
        }

        // Assert
        $this->assertTrue($exceptionThrown);
        $this->assertEquals($message, $caughtMessage);
    }

    /**
     * Test specific error messages from requirements
     */
    public function test_it_handles_requirement_specific_error_messages(): void
    {
        // Test various error messages from the requirements document
        $errorMessages = [
            'Silakan pilih Tahun Ajaran tujuan',                      // Req 7.1
            'Tahun Ajaran tujuan sama dengan yang aktif',             // Req 7.2
            'Tidak ada siswa aktif yang akan di-rollover',            // Req 7.7
            'Data tidak valid: Kelas lebih dari 9',                   // Req 18.4
            'Data tidak valid: Kelas kurang dari 7',                  // Req 18.5
        ];

        foreach ($errorMessages as $message) {
            $exception = new RolloverValidationException($message);
            $this->assertEquals($message, $exception->getMessage());
            $this->assertEquals(422, $exception->getCode());
        }
    }
}
