<?php

namespace phplum\fs\tests\unit;

use PHPUnit\Framework\TestCase;
use phplum\fs\Path;

/**
 * Test cases for class phplum\fs\Path
 */
class PathTest extends TestCase
{
    /**
     * Tests method Path::__construct()
     *
     * @return void
     */
    public function testConstructor(): void
    {
        $this->assertEquals('', new Path(''));
        $this->assertEquals(DIRECTORY_SEPARATOR, new Path('', DIRECTORY_SEPARATOR));
        $this->assertEquals(
            'path1' . DIRECTORY_SEPARATOR . 'path2',
            new Path('path1', 'path2')
        );
        $this->assertEquals(
            DIRECTORY_SEPARATOR . 'path1' . DIRECTORY_SEPARATOR . 'path2',
            new Path(DIRECTORY_SEPARATOR . 'path1', 'path2')
        );
        $this->assertEquals('path1', new Path('', 'path1'));
        $this->assertEquals(
            'path1' . DIRECTORY_SEPARATOR . 'path2' . DIRECTORY_SEPARATOR . 'path3',
            new Path('path1', 'path2', 'path3')
        );
    }

    /**
     * Tests property `parent`.
     *
     * @return void
     */
    public function testPropertyParent(): void
    {
        $path = new Path('path', 'to', 'file');

        $this->assertEquals('path' . DIRECTORY_SEPARATOR . 'to', $path->parent);
        $this->assertEquals('phplum\fs\Path', $path->parent::class);
    }

    /**
     * Tests trying to get unknown properties.
     *
     * @return void
     */
    public function testUnknownProperty(): void
    {
        $path = new Path('');

        $this->expectException(\RuntimeException::class);

        // @phpstan-ignore-next-line
        $path->unknownProperty;
    }
}
