<?php

namespace phplum\fs\tests\unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function testFoo(): void
    {
        // @phpstan-ignore method.alreadyNarrowedType
        $this->assertTrue(true);
    }
}
