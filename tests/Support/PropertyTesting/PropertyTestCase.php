<?php

namespace Tests\Support\PropertyTesting;

use Tests\TestCase;

/**
 * Base class for property-based tests in this project.
 *
 * Extends the project's standard TestCase (which sets up Laravel's testing
 * infrastructure) and mixes in PropertyTestTrait for the forAll() API.
 *
 * Property-based tests in tests/Feature/Auth/ should extend this class:
 *
 *   class MyPropertyTest extends PropertyTestCase { ... }
 */
abstract class PropertyTestCase extends TestCase
{
    use PropertyTestTrait;
}
