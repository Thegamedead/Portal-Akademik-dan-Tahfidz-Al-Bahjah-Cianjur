<?php

namespace Tests\Unit\Services\Laporan;

use App\Services\Laporan\FilterService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Unit Tests for FilterService
 *
 * Tests filter validation, date range validation, and filter array building
 * for the Laporan module. Validates Requirement 11 (Filter Panel) implementation.
 */
class FilterServiceTest extends TestCase
{
    protected FilterService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FilterService();
    }

    /**
     * @test
     */
    public function test_it_validates_correct_date_range()
    {
        // Arrange
        $startDate = '2024-01-01';
        $endDate = '2024-01-31';

        // Act
        $result = $this->service->validateDateRange($startDate, $endDate);

        // Assert
        $this->assertTrue($result['valid']);
        $this->assertNull($result['message']);
        $this->assertInstanceOf(Carbon::class, $result['start']);
        $this->assertInstanceOf(Carbon::class, $result['end']);
        $this->assertEquals('2024-01-01', $result['start']->toDateString());
        $this->assertEquals('2024-01-31', $result['end']->toDateString());
    }

    /**
     * @test
     */
    public function test_it_rejects_date_range_where_start_is_after_end()
    {
        // Arrange
        $startDate = '2024-12-31';
        $endDate = '2024-01-01';

        // Act
        $result = $this->service->validateDateRange($startDate, $endDate);

        // Assert
        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('tidak boleh lebih besar', $result['message']);
    }

    /**
     * @test
     */
    public function test_it_rejects_future_start_date()
    {
        // Arrange
        $futureDate = Carbon::today()->addDays(10)->toDateString();
        $endDate = Carbon::today()->addDays(20)->toDateString();

        // Act
        $result = $this->service->validateDateRange($futureDate, $endDate);

        // Assert
        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('tidak boleh di masa depan', $result['message']);
    }

    /**
     * @test
     */
    public function test_it_rejects_future_end_date()
    {
        // Arrange
        $startDate = Carbon::today()->toDateString();
        $futureDate = Carbon::today()->addDays(10)->toDateString();

        // Act
        $result = $this->service->validateDateRange($startDate, $futureDate);

        // Assert
        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('tidak boleh di masa depan', $result['message']);
    }

    /**
     * @test
     */
    public function test_it_rejects_date_range_exceeding_max_days()
    {
        // Arrange - using 30 day max for this test
        $service = new FilterService(30);
        $startDate = '2024-01-01';
        $endDate = '2024-12-31'; // 365 days apart

        // Act
        $result = $service->validateDateRange($startDate, $endDate);

        // Assert
        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('tidak boleh lebih dari', $result['message']);
    }

    /**
     * @test
     */
    public function test_it_accepts_date_range_within_max_days()
    {
        // Arrange - 365 days is the default max
        $startDate = '2024-01-01';
        $endDate = '2024-12-31'; // 365 days apart

        // Act
        $result = $this->service->validateDateRange($startDate, $endDate);

        // Assert
        $this->assertTrue($result['valid']);
        $this->assertNull($result['message']);
    }

    /**
     * @test
     */
    public function test_it_accepts_null_date_range()
    {
        // Act
        $result = $this->service->validateDateRange(null, null);

        // Assert
        $this->assertTrue($result['valid']);
        $this->assertNull($result['message']);
        $this->assertNull($result['start']);
        $this->assertNull($result['end']);
    }

    /**
     * @test
     */
    public function test_it_rejects_invalid_date_format()
    {
        // Arrange
        $invalidDate = 'not-a-date';
        $endDate = '2024-01-31';

        // Act
        $result = $this->service->validateDateRange($invalidDate, $endDate);

        // Assert
        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('Format tanggal tidak valid', $result['message']);
    }

    /**
     * @test
     */
    public function test_it_parses_date_range_correctly()
    {
        // Arrange
        $startDate = '2024-01-01';
        $endDate = '2024-01-31';

        // Act
        $result = $this->service->parseDateRange($startDate, $endDate);

        // Assert
        $this->assertInstanceOf(Carbon::class, $result['start']);
        $this->assertInstanceOf(Carbon::class, $result['end']);
        $this->assertEquals('2024-01-01', $result['start']->toDateString());
        $this->assertEquals('2024-01-31', $result['end']->toDateString());
    }

    /**
     * @test
     */
    public function test_it_handles_carbon_instances_in_parse_date_range()
    {
        // Arrange
        $startCarbon = Carbon::parse('2024-01-01');
        $endCarbon = Carbon::parse('2024-01-31');

        // Act
        $result = $this->service->parseDateRange($startCarbon, $endCarbon);

        // Assert
        $this->assertSame($startCarbon, $result['start']);
        $this->assertSame($endCarbon, $result['end']);
    }

    /**
     * @test
     */
    public function test_it_validates_positive_integer_id_filter()
    {
        // Arrange
        $filterName = 'siswa_id';
        $filterValue = 123;

        // Act
        $result = $this->service->validateFilter($filterName, $filterValue);

        // Assert
        $this->assertTrue($result['valid']);
        $this->assertNull($result['message']);
        $this->assertEquals(123, $result['value']);
    }

    /**
     * @test
     */
    public function test_it_rejects_negative_id_filter()
    {
        // Arrange
        $filterName = 'kelompok_id';
        $filterValue = -5;

        // Act
        $result = $this->service->validateFilter($filterName, $filterValue);

        // Assert
        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('harus berupa angka positif', $result['message']);
    }

    /**
     * @test
     */
    public function test_it_rejects_zero_id_filter()
    {
        // Arrange
        $filterName = 'siswa_id';
        $filterValue = 0;

        // Act
        $result = $this->service->validateFilter($filterName, $filterValue);

        // Assert
        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('harus berupa angka positif', $result['message']);
    }

    /**
     * @test
     */
    public function test_it_rejects_non_numeric_id_filter()
    {
        // Arrange
        $filterName = 'siswa_id';
        $filterValue = 'abc';

        // Act
        $result = $this->service->validateFilter($filterName, $filterValue);

        // Assert
        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('harus berupa angka positif', $result['message']);
    }

    /**
     * @test
     */
    public function test_it_validates_enum_string_filter()
    {
        // Arrange
        $filterName = 'status';
        $filterValue = 'Aktif';

        // Act
        $result = $this->service->validateFilter($filterName, $filterValue);

        // Assert
        $this->assertTrue($result['valid']);
        $this->assertNull($result['message']);
        $this->assertEquals('Aktif', $result['value']);
    }

    /**
     * @test
     */
    public function test_it_trims_whitespace_from_string_filters()
    {
        // Arrange
        $filterName = 'tingkat';
        $filterValue = '  Dasar  ';

        // Act
        $result = $this->service->validateFilter($filterName, $filterValue);

        // Assert
        $this->assertTrue($result['valid']);
        $this->assertEquals('Dasar', $result['value']);
    }

    /**
     * @test
     */
    public function test_it_accepts_null_filter_value()
    {
        // Arrange
        $filterName = 'siswa_id';
        $filterValue = null;

        // Act
        $result = $this->service->validateFilter($filterName, $filterValue);

        // Assert
        $this->assertTrue($result['valid']);
        $this->assertNull($result['value']);
    }

    /**
     * @test
     */
    public function test_it_treats_empty_string_as_null()
    {
        // Arrange
        $filterName = 'status';
        $filterValue = '';

        // Act
        $result = $this->service->validateFilter($filterName, $filterValue);

        // Assert
        $this->assertTrue($result['valid']);
        $this->assertNull($result['value']);
    }

    /**
     * @test
     */
    public function test_it_builds_filter_array_from_valid_request()
    {
        // Arrange
        $request = Request::create('/laporan', 'GET', [
            'tanggal_mulai' => '2024-01-01',
            'tanggal_selesai' => '2024-01-31',
            'siswa_id' => 5,
            'kelompok_id' => 10,
            'status' => 'Aktif',
        ]);

        // Act
        $result = $this->service->buildFilterArray($request);

        // Assert
        $this->assertTrue($result['valid']);
        $this->assertNull($result['message']);
        $this->assertArrayHasKey('tanggal_mulai', $result['filters']);
        $this->assertArrayHasKey('tanggal_selesai', $result['filters']);
        $this->assertArrayHasKey('siswa_id', $result['filters']);
        $this->assertArrayHasKey('kelompok_id', $result['filters']);
        $this->assertArrayHasKey('status', $result['filters']);
        $this->assertEquals(5, $result['filters']['siswa_id']);
        $this->assertEquals(10, $result['filters']['kelompok_id']);
        $this->assertEquals('Aktif', $result['filters']['status']);
    }

    /**
     * @test
     */
    public function test_it_rejects_filter_array_with_invalid_date_range()
    {
        // Arrange
        $request = Request::create('/laporan', 'GET', [
            'tanggal_mulai' => '2024-12-31',
            'tanggal_selesai' => '2024-01-01',
            'siswa_id' => 5,
        ]);

        // Act
        $result = $this->service->buildFilterArray($request);

        // Assert
        $this->assertFalse($result['valid']);
        $this->assertNotNull($result['message']);
        $this->assertEmpty($result['filters']);
    }

    /**
     * @test
     */
    public function test_it_rejects_filter_array_with_invalid_id()
    {
        // Arrange
        $request = Request::create('/laporan', 'GET', [
            'siswa_id' => -5,
        ]);

        // Act
        $result = $this->service->buildFilterArray($request);

        // Assert
        $this->assertFalse($result['valid']);
        $this->assertNotNull($result['message']);
        $this->assertEmpty($result['filters']);
    }

    /**
     * @test
     */
    public function test_it_builds_empty_filter_array_for_empty_request()
    {
        // Arrange
        $request = Request::create('/laporan', 'GET', []);

        // Act
        $result = $this->service->buildFilterArray($request);

        // Assert
        $this->assertTrue($result['valid']);
        $this->assertNull($result['message']);
        $this->assertEmpty($result['filters']);
    }

    /**
     * @test
     */
    public function test_it_skips_null_values_in_filter_array()
    {
        // Arrange
        $request = Request::create('/laporan', 'GET', [
            'siswa_id' => 5,
            'kelompok_id' => null,
            'status' => 'Aktif',
        ]);

        // Act
        $result = $this->service->buildFilterArray($request);

        // Assert
        $this->assertTrue($result['valid']);
        $this->assertArrayHasKey('siswa_id', $result['filters']);
        $this->assertArrayNotHasKey('kelompok_id', $result['filters']);
        $this->assertArrayHasKey('status', $result['filters']);
    }

    /**
     * @test
     */
    public function test_it_handles_multiple_validation_errors()
    {
        // Arrange
        $request = Request::create('/laporan', 'GET', [
            'tanggal_mulai' => '2024-12-31',
            'tanggal_selesai' => '2024-01-01',
            'siswa_id' => -5,
        ]);

        // Act
        $result = $this->service->buildFilterArray($request);

        // Assert
        $this->assertFalse($result['valid']);
        $this->assertNotNull($result['message']);
        // Message should contain multiple error messages
        $this->assertStringContainsString('tidak boleh lebih besar', $result['message']);
    }
}
