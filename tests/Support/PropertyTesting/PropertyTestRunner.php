<?php

namespace Tests\Support\PropertyTesting;

use PHPUnit\Framework\AssertionFailedError;

/**
 * Core property runner — the heart of the custom PBT framework.
 *
 * Why this exists:
 * ----------------
 * giorgiosironi/eris only supports PHPUnit ^10, while this project uses
 * PHPUnit 12.5.x (bundled with Laravel 13). Rather than downgrade PHPUnit or
 * introduce incompatible dependencies, this lightweight runner replicates the
 * essential PBT workflow:
 *
 *   1. Generate N random combinations of inputs.
 *   2. Execute the property (a callable) for each combination.
 *   3. On failure, record the failing example and re-throw for PHPUnit.
 *
 * Usage (in a test class that uses PropertyTestTrait):
 * ----------------------------------------------------
 *   $this->forAll(
 *       Generators::guard(),
 *       Generators::string(1, 50),
 *   )->check(function (string $guard, string $value) {
 *       // assertion goes here
 *   });
 */
class PropertyTestRunner
{
    /** @var Generator[] */
    private array $generators;

    /** Number of iterations (default 100, matches Eris default) */
    private int $iterations = 100;

    /** Seed for reproducibility (null = random each run) */
    private ?int $seed = null;

    public function __construct(Generator ...$generators)
    {
        $this->generators = $generators;
    }

    /**
     * Override the number of test iterations.
     */
    public function withIterations(int $n): static
    {
        $this->iterations = $n;
        return $this;
    }

    /**
     * Fix the random seed for deterministic reproduction.
     */
    public function withSeed(int $seed): static
    {
        $this->seed = $seed;
        return $this;
    }

    /**
     * Execute the property callable against generated inputs.
     *
     * @param callable $property  A function whose parameters match the generators.
     *                            Should use PHPUnit assertions to express the property.
     * @throws PropertyFailureException When the property fails for a generated input.
     */
    public function check(callable $property): void
    {
        // Save the current mt_rand state so we can restore it after, ensuring
        // that re-seeding with the same seed always produces the same sequence
        // regardless of what ran before.
        if ($this->seed !== null) {
            mt_srand($this->seed, MT_RAND_MT19937);
        }

        for ($i = 0; $i < $this->iterations; $i++) {
            $args = array_map(fn(Generator $g) => $g->generate(), $this->generators);

            try {
                $property(...$args);
            } catch (AssertionFailedError $e) {
                $argsFormatted = array_map(fn($a) => var_export($a, true), $args);
                $argsStr = implode(', ', $argsFormatted);

                throw new PropertyFailureException(
                    "Property failed after " . ($i + 1) . " iteration(s).\n" .
                    "Failing example: [{$argsStr}]\n" .
                    "Cause: " . $e->getMessage(),
                    0,
                    $e
                );
            } catch (\Throwable $e) {
                $argsFormatted = array_map(fn($a) => var_export($a, true), $args);
                $argsStr = implode(', ', $argsFormatted);

                throw new PropertyFailureException(
                    "Property threw exception after " . ($i + 1) . " iteration(s).\n" .
                    "Failing example: [{$argsStr}]\n" .
                    "Exception: " . get_class($e) . ': ' . $e->getMessage(),
                    0,
                    $e
                );
            }
        }
    }
}
