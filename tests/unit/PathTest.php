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
     * Test method Path::__construct()
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
}
