<?php

namespace Tests\Unit\PropertyTesting;

use Tests\Support\PropertyTesting\Generators;
use Tests\Support\PropertyTesting\PropertyFailureException;
use Tests\Support\PropertyTesting\PropertyTestCase;

/**
 * Smoke tests for the custom PBT framework setup.
 *
 * Verifies:
 * - Generators produce values of the correct type and shape.
 * - PropertyTestRunner runs the property N times.
 * - Failing properties surface the failing example clearly.
 * - PropertyTestCase is loadable and usable.
 */
class PropertyTestingSetupTest extends PropertyTestCase
{
    // -----------------------------------------------------------------------
    // Generator smoke tests
    // -----------------------------------------------------------------------

    public function test_string_generator_returns_string_within_bounds(): void
    {
        $gen = Generators::string(3, 10);
        for ($i = 0; $i < 50; $i++) {
            $value = $gen->generate();
            $this->assertIsString($value);
            $this->assertGreaterThanOrEqual(3, strlen($value));
            $this->assertLessThanOrEqual(10, strlen($value));
        }
    }

    public function test_int_generator_returns_integer_within_bounds(): void
    {
        $gen = Generators::int(5, 20);
        for ($i = 0; $i < 50; $i++) {
            $value = $gen->generate();
            $this->assertIsInt($value);
            $this->assertGreaterThanOrEqual(5, $value);
            $this->assertLessThanOrEqual(20, $value);
        }
    }

    public function test_choose_generator_returns_only_given_values(): void
    {
        $allowed = ['admin', 'guru', 'siswa'];
        $gen = Generators::choose($allowed);
        for ($i = 0; $i < 50; $i++) {
            $this->assertContains($gen->generate(), $allowed);
        }
    }

    public function test_date_generator_returns_valid_date_string(): void
    {
        $gen = Generators::date('1990-01-01', '2005-12-31');
        for ($i = 0; $i < 50; $i++) {
            $value = $gen->generate();
            $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $value);
            $this->assertGreaterThanOrEqual('1990-01-01', $value);
            $this->assertLessThanOrEqual('2005-12-31', $value);
        }
    }

    public function test_niy_generator_returns_correct_format(): void
    {
        $gen = Generators::niy();
        for ($i = 0; $i < 50; $i++) {
            $value = $gen->generate();
            $this->assertMatchesRegularExpression('/^\d{2}-\d{3}$/', $value, "NIY '{$value}' tidak sesuai format YY-NNN");
        }
    }

    public function test_nis_generator_returns_numeric_string(): void
    {
        $gen = Generators::nis();
        for ($i = 0; $i < 50; $i++) {
            $value = $gen->generate();
            $this->assertMatchesRegularExpression('/^\d{5,10}$/', $value, "NIS '{$value}' bukan numeric 5-10 digit");
        }
    }

    public function test_ipv4_generator_returns_valid_address(): void
    {
        $gen = Generators::ipv4();
        for ($i = 0; $i < 50; $i++) {
            $value = $gen->generate();
            $this->assertNotFalse(
                filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4),
                "'{$value}' bukan IPv4 yang valid"
            );
        }
    }

    public function test_guard_generator_returns_valid_guard_name(): void
    {
        $gen = Generators::guard();
        for ($i = 0; $i < 50; $i++) {
            $this->assertContains($gen->generate(), ['admin', 'guru', 'siswa']);
        }
    }

    public function test_username_generator_returns_string_6_to_20_chars(): void
    {
        $gen = Generators::username();
        for ($i = 0; $i < 50; $i++) {
            $value = $gen->generate();
            $len = strlen($value);
            $this->assertGreaterThanOrEqual(6, $len);
            $this->assertLessThanOrEqual(20, $len);
        }
    }

    public function test_nama_generator_returns_multi_word_string(): void
    {
        $gen = Generators::nama();
        for ($i = 0; $i < 20; $i++) {
            $value = $gen->generate();
            $this->assertStringContainsString(' ', $value, "Nama harus minimal 2 kata: '{$value}'");
        }
    }

    // -----------------------------------------------------------------------
    // PropertyTestRunner / forAll smoke tests
    // -----------------------------------------------------------------------

    public function test_for_all_runs_property_100_times_by_default(): void
    {
        $count = 0;
        $this->forAll(Generators::int(1, 10))->check(function (int $n) use (&$count) {
            $count++;
            $this->assertGreaterThanOrEqual(1, $n);
        });
        $this->assertSame(100, $count, 'forAll() harus menjalankan property tepat 100 kali secara default');
    }

    public function test_for_all_respects_custom_iteration_count(): void
    {
        $count = 0;
        $this->forAll(Generators::int(1, 10))
            ->withIterations(37)
            ->check(function (int $n) use (&$count) {
                $count++;
            });
        $this->assertSame(37, $count);
    }

    public function test_for_all_passes_multiple_generators_as_separate_args(): void
    {
        $this->forAll(
            Generators::guard(),
            Generators::int(1, 100),
            Generators::date(),
        )->withIterations(20)->check(function (string $guard, int $n, string $date) {
            $this->assertContains($guard, ['admin', 'guru', 'siswa']);
            $this->assertGreaterThanOrEqual(1, $n);
            $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $date);
        });
    }

    public function test_failing_property_surfaces_counterexample(): void
    {
        $this->expectException(PropertyFailureException::class);
        $this->expectExceptionMessageMatches('/Failing example:/');

        $this->forAll(Generators::int(1, 10))->check(function (int $n) {
            // This property is always false — should surface the counterexample
            $this->assertSame(0, $n, 'Nilai tidak boleh positif');
        });
    }

    public function test_seed_option_is_accepted_and_does_not_throw(): void
    {
        // Verify that withSeed() is accepted and the property runs without error.
        // True cross-run determinism depends on mt_rand global state which can be
        // affected by other code; this test validates the API contract only.
        $count = 0;
        $this->forAll(Generators::int(1, 1000))
            ->withSeed(42)
            ->withIterations(10)
            ->check(function (int $n) use (&$count) {
                $this->assertGreaterThanOrEqual(1, $n);
                $this->assertLessThanOrEqual(1000, $n);
                $count++;
            });
        $this->assertSame(10, $count, 'Harus berjalan tepat 10 iterasi dengan seed 42');
    }
}
