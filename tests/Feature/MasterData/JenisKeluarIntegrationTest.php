<?php

namespace Tests\Feature\MasterData;

use App\Enums\JenisKeluar;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * Integration test for JenisKeluar enum with Siswa model
 *
 * This test suite verifies:
 * - JenisKeluar enum values can be stored in siswas.jenis_keluar column
 * - Laravel validation works correctly with JenisKeluar::validationRule()
 * - Enum integration with database and model layer
 *
 * Validates: Requirements FR-1.2.1, FR-1.5.2
 */
class JenisKeluarIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: JenisKeluar enum values can be stored in database
     * Validates: Requirements FR-1.2.1
     */
    public function test_jenis_keluar_enum_values_can_be_stored_in_database(): void
    {
        // Test each enum case
        $enumCases = [
            JenisKeluar::LULUS_SMP,
            JenisKeluar::PINDAH_SEKOLAH,
            JenisKeluar::MENINGGAL,
            JenisKeluar::KELUAR,
        ];

        foreach ($enumCases as $jenisKeluar) {
            // Arrange: Create a minimal siswa record
            $siswa = Siswa::factory()->create([
                'jenis_keluar' => $jenisKeluar->value,
                'tanggal_keluar' => now(),
            ]);

            // Assert: Value is stored correctly
            $this->assertDatabaseHas('siswas', [
                'id' => $siswa->id,
                'jenis_keluar' => $jenisKeluar->value,
            ]);

            // Assert: Model retrieves the value correctly
            $retrievedSiswa = Siswa::find($siswa->id);
            $this->assertEquals($jenisKeluar->value, $retrievedSiswa->jenis_keluar);
        }
    }

    /**
     * Test: JenisKeluar::validationRule() works with Laravel Validator
     * Validates: Requirements FR-1.5.2
     */
    public function test_jenis_keluar_validation_rule_accepts_valid_values(): void
    {
        // Test each valid enum value
        $validValues = JenisKeluar::values();

        foreach ($validValues as $value) {
            // Act
            $validator = Validator::make(
                ['jenis_keluar' => $value],
                ['jenis_keluar' => ['required', JenisKeluar::validationRule()]]
            );

            // Assert: Validation passes
            $this->assertFalse($validator->fails(), "Validation should pass for value: {$value}");
        }
    }

    /**
     * Test: JenisKeluar::validationRule() rejects invalid values
     * Validates: Requirements FR-1.5.2
     */
    public function test_jenis_keluar_validation_rule_rejects_invalid_values(): void
    {
        // Arrange: Invalid values
        $invalidValues = [
            'Invalid Value',
            'Graduate',
            'Transfer',
            '',
            null,
            123,
        ];

        foreach ($invalidValues as $value) {
            // Act
            $validator = Validator::make(
                ['jenis_keluar' => $value],
                ['jenis_keluar' => ['required', JenisKeluar::validationRule()]]
            );

            // Assert: Validation fails
            $this->assertTrue($validator->fails(), "Validation should fail for value: " . var_export($value, true));
        }
    }

    /**
     * Test: JenisKeluar can be instantiated from database value
     * Validates: Requirements FR-1.2.1
     */
    public function test_jenis_keluar_can_be_instantiated_from_database_value(): void
    {
        // Arrange
        $siswa = Siswa::factory()->create([
            'jenis_keluar' => 'Pindah Sekolah',
            'tanggal_keluar' => now(),
        ]);

        // Act
        $retrievedSiswa = Siswa::find($siswa->id);
        $jenisKeluarEnum = JenisKeluar::from($retrievedSiswa->jenis_keluar);

        // Assert
        $this->assertEquals(JenisKeluar::PINDAH_SEKOLAH, $jenisKeluarEnum);
        $this->assertEquals('Pindah Sekolah', $jenisKeluarEnum->value);
    }

    /**
     * Test: JenisKeluar options can be used in dropdown generation
     * Validates: Requirements 2.1
     */
    public function test_jenis_keluar_options_suitable_for_dropdown(): void
    {
        // Act
        $options = JenisKeluar::options();

        // Assert: Options structure is correct for HTML select dropdown
        $this->assertIsArray($options);
        $this->assertNotEmpty($options);

        foreach ($options as $value => $label) {
            $this->assertIsString($value, 'Dropdown value should be string');
            $this->assertIsString($label, 'Dropdown label should be string');
            $this->assertNotEmpty($value, 'Dropdown value should not be empty');
            $this->assertNotEmpty($label, 'Dropdown label should not be empty');
        }

        // Assert: "Keluar" has descriptive label "(DO)"
        $this->assertEquals('Keluar (DO)', $options['Keluar']);
    }

    /**
     * Test: Database constraint allows null for jenis_keluar
     * Validates: Requirements 13.1, 13.2
     */
    public function test_jenis_keluar_field_allows_null_for_active_students(): void
    {
        // Arrange: Create siswa without jenis_keluar (active student)
        $siswa = Siswa::factory()->create([
            'jenis_keluar' => null,
            'tanggal_keluar' => null,
        ]);

        // Assert: NULL value is stored correctly
        $this->assertDatabaseHas('siswas', [
            'id' => $siswa->id,
            'jenis_keluar' => null,
        ]);

        // Assert: Model retrieves NULL correctly
        $retrievedSiswa = Siswa::find($siswa->id);
        $this->assertNull($retrievedSiswa->jenis_keluar);
    }
}
