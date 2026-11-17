<?php

namespace Tests\Support\PropertyTesting;

use PHPUnit\Framework\AssertionFailedError;

/**
 * Thrown by PropertyTestRunner when a property fails.
 *
 * Extends AssertionFailedError so PHPUnit treats the failure as a test
 * assertion failure (red, not error) and shows the failing example clearly.
 */
class PropertyFailureException extends AssertionFailedError
{
    // Inherits all PHPUnit assertion failure behaviour.
}
