<?php

namespace Tests\Unit\Enums;

use App\Enums\JenisKeluar;
use PHPUnit\Framework\TestCase;

/**
 * Test suite for JenisKeluar enum
 *
 * This test suite verifies the following:
 * - All enum cases are defined correctly
 * - values() method returns all enum values
 * - options() method returns correct dropdown options
 * - validationRule() method generates correct Laravel validation rule
 *
 * Validates: Requirements 2.1 (FR-1.2.1, FR-1.5.2)
 */
class JenisKeluarTest extends TestCase
{
    /**
     * Test: All enum cases are defined with correct values
     * Validates: Requirements FR-1.2.1
     */
    public function test_enum_cases_are_defined_correctly(): void
    {
        // Assert: All four cases exist
        $this->assertEquals('Lulus SMP', JenisKeluar::LULUS_SMP->value);
        $this->assertEquals('Pindah Sekolah', JenisKeluar::PINDAH_SEKOLAH->value);
        $this->assertEquals('Meninggal', JenisKeluar::MENINGGAL->value);
        $this->assertEquals('Keluar', JenisKeluar::KELUAR->value);
    }

    /**
     * Test: values() returns all enum values as array
     * Validates: Requirements 2.1
     */
    public function test_values_method_returns_all_enum_values(): void
    {
        // Act
        $values = JenisKeluar::values();

        // Assert
        $this->assertIsArray($values);
        $this->assertCount(4, $values);
        $this->assertContains('Lulus SMP', $values);
        $this->assertContains('Pindah Sekolah', $values);
        $this->assertContains('Meninggal', $values);
        $this->assertContains('Keluar', $values);
    }

    /**
     * Test: options() returns correct dropdown options with labels
     * Validates: Requirements 2.1
     */
    public function test_options_method_returns_correct_dropdown_options(): void
    {
        // Act
        $options = JenisKeluar::options();

        // Assert
        $this->assertIsArray($options);
        $this->assertCount(4, $options);

        // Check value => label mapping
        $this->assertArrayHasKey('Lulus SMP', $options);
        $this->assertEquals('Lulus SMP', $options['Lulus SMP']);

        $this->assertArrayHasKey('Pindah Sekolah', $options);
        $this->assertEquals('Pindah Sekolah', $options['Pindah Sekolah']);

        $this->assertArrayHasKey('Meninggal', $options);
        $this->assertEquals('Meninggal', $options['Meninggal']);

        $this->assertArrayHasKey('Keluar', $options);
        $this->assertEquals('Keluar (DO)', $options['Keluar']);
    }

    /**
     * Test: validationRule() generates correct Laravel validation rule
     * Validates: Requirements FR-1.5.2
     */
    public function test_validation_rule_generates_correct_rule_string(): void
    {
        // Act
        $rule = JenisKeluar::validationRule();

        // Assert
        $this->assertIsString($rule);
        $this->assertStringStartsWith('in:', $rule);

        // Verify all values are in the rule
        $this->assertStringContainsString('Lulus SMP', $rule);
        $this->assertStringContainsString('Pindah Sekolah', $rule);
        $this->assertStringContainsString('Meninggal', $rule);
        $this->assertStringContainsString('Keluar', $rule);

        // Verify format is correct (in:value1,value2,value3,value4)
        $expectedRule = 'in:Lulus SMP,Pindah Sekolah,Meninggal,Keluar';
        $this->assertEquals($expectedRule, $rule);
    }

    /**
     * Test: cases() returns all enum cases
     * Validates: Requirements FR-1.2.1
     */
    public function test_cases_method_returns_all_enum_cases(): void
    {
        // Act
        $cases = JenisKeluar::cases();

        // Assert
        $this->assertIsArray($cases);
        $this->assertCount(4, $cases);

        // Verify each case is an instance of JenisKeluar
        foreach ($cases as $case) {
            $this->assertInstanceOf(JenisKeluar::class, $case);
        }
    }

    /**
     * Test: Enum can be instantiated from string value
     * Validates: Requirements FR-1.5.2
     */
    public function test_enum_can_be_instantiated_from_string_value(): void
    {
        // Act & Assert
        $this->assertEquals(JenisKeluar::LULUS_SMP, JenisKeluar::from('Lulus SMP'));
        $this->assertEquals(JenisKeluar::PINDAH_SEKOLAH, JenisKeluar::from('Pindah Sekolah'));
        $this->assertEquals(JenisKeluar::MENINGGAL, JenisKeluar::from('Meninggal'));
        $this->assertEquals(JenisKeluar::KELUAR, JenisKeluar::from('Keluar'));
    }

    /**
     * Test: Enum throws exception for invalid value
     * Validates: Requirements FR-1.5.2
     */
    public function test_enum_throws_exception_for_invalid_value(): void
    {
        // Assert
        $this->expectException(\ValueError::class);

        // Act
        JenisKeluar::from('Invalid Value');
    }

    /**
     * Test: tryFrom returns null for invalid value
     * Validates: Requirements FR-1.5.2
     */
    public function test_try_from_returns_null_for_invalid_value(): void
    {
        // Act
        $result = JenisKeluar::tryFrom('Invalid Value');

        // Assert
        $this->assertNull($result);
    }

    /**
     * Test: tryFrom returns enum for valid value
     * Validates: Requirements FR-1.5.2
     */
    public function test_try_from_returns_enum_for_valid_value(): void
    {
        // Act & Assert
        $this->assertEquals(JenisKeluar::LULUS_SMP, JenisKeluar::tryFrom('Lulus SMP'));
        $this->assertEquals(JenisKeluar::PINDAH_SEKOLAH, JenisKeluar::tryFrom('Pindah Sekolah'));
        $this->assertEquals(JenisKeluar::MENINGGAL, JenisKeluar::tryFrom('Meninggal'));
        $this->assertEquals(JenisKeluar::KELUAR, JenisKeluar::tryFrom('Keluar'));
    }
}
