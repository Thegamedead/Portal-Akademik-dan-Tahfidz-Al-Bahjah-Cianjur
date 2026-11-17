<?php

namespace Tests\Support\PropertyTesting;

/**
 * Base class for value generators used in property-based tests.
 *
 * Provides a fluent API for creating typed random value streams
 * compatible with PHPUnit 12 — used as an alternative to giorgiosironi/eris
 * which only supports PHPUnit ^10.
 */
abstract class Generator
{
    /**
     * Generate a single random value.
     */
    abstract public function generate(): mixed;

    /**
     * Generate N values, returning an array.
     *
     * @return array<mixed>
     */
    public function sample(int $count = 10): array
    {
        $samples = [];
        for ($i = 0; $i < $count; $i++) {
            $samples[] = $this->generate();
        }
        return $samples;
    }
}
