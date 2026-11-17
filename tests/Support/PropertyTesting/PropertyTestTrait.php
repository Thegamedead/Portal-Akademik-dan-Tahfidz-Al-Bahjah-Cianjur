<?php

namespace Tests\Support\PropertyTesting;

/**
 * Trait to be used in PHPUnit test classes to enable property-based testing.
 *
 * Usage:
 * ------
 *   use Tests\Support\PropertyTesting\PropertyTestTrait;
 *   use Tests\Support\PropertyTesting\Generators;
 *
 *   class MyPropertyTest extends TestCase
 *   {
 *       use PropertyTestTrait;
 *
 *       public function test_some_property(): void
 *       {
 *           $this->forAll(
 *               Generators::guard(),
 *               Generators::string(1, 50),
 *           )->check(function (string $guard, string $value) {
 *               $this->assertNotEmpty($guard);
 *           });
 *       }
 *   }
 *
 * Customisation:
 * --------------
 *   $this->forAll(...)->withIterations(50)->check(...);
 *   $this->forAll(...)->withSeed(42)->check(...);
 */
trait PropertyTestTrait
{
    /**
     * Create a PropertyTestRunner for the given generators.
     * Each generator corresponds to one argument in the property callable.
     */
    protected function forAll(Generator ...$generators): PropertyTestRunner
    {
        return new PropertyTestRunner(...$generators);
    }
}
