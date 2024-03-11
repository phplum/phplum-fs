<?php

namespace phplum\fs\tests\unit;

use Exception;
use PHPUnit\Framework\TestCase;
use phplum\fs\FileSystem;
use phplum\fs\Path;
use phplum\fs\IOException;
use phplum\fs\FileNotFoundException;

/**
 * Test cases for class phplum\fs\FileSystem
 */
class FileSystemTest extends TestCase
{
    /**
     * Working directory.
     *
     * @var Path
     */
    private Path $workDir;

    /**
     * Previous umask
     *
     * @var integer
     */
    private int $umask;

    /**
     * Sets up this unit test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->workDir = new Path(sys_get_temp_dir(), uniqid('phplum.tests.'));
        FileSystem::mkdir($this->workDir);
        $this->assertTrue(is_dir($this->workDir) && is_writable($this->workDir));

        $this->umask = umask(0);
    }

    /**
     * Removes artifact.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        FileSystem::rm($this->workDir, true);
        $this->assertFalse(file_exists($this->workDir));

        umask($this->umask);
    }

    /**
     * Tests method `mkdir`
     *
     * @return void
     */
    public function testMkdir(): void
    {
        $path = new Path($this->workDir, uniqid());

        FileSystem::mkdir($path);
        $this->assertTrue(is_dir($path));
        $this->assertEquals(0777, (fileperms($path) & 0777));

        $subPath = new Path($path, uniqid());
        FileSystem::mkdir($subPath, 0755, true);
        $this->assertTrue(is_dir($subPath));
        $this->assertEquals(0755, (fileperms($path) & 0755));
        $this->assertEquals(0755, (fileperms($subPath) & 0755));

        FileSystem::rm($path, true);

        $this->expectException(IOException::class);
        FileSystem::mkdir(new Path($path, uniqid()));
    }

    /**
     * Tests method `rm`
     *
     * @return void
     * @throws Exception If not able to generate random bytes.
     */
    public function testRm(): void
    {
        $path = new Path($this->workDir, uniqid());
        FileSystem::mkdir($path);
        FileSystem::writeFile(new Path($path, uniqid()), random_bytes(64));

        FileSystem::mkdir(new Path($path, uniqid()));

        FileSystem::rm($path, true);
        $this->assertFalse(file_exists($path));

        $this->expectException(FileNotFoundException::class);
        FileSystem::rm(uniqid());
    }

    /**
     * Tests method `unlink`
     *
     * @return void
     * @throws Exception If not able to generate random bytes.
     */
    public function testUnlink(): void
    {
        $path = new Path($this->workDir, uniqid());
        FileSystem::writeFile($path, random_bytes(64));

        FileSystem::unlink($path);
        $this->assertFalse(file_exists($path));

        $this->expectException(FileNotFoundException::class);
        FileSystem::unlink($path);
    }

    /**
     * Tests method `writeFile`
     *
     * @return void
     * @throws Exception If not able to generate random bytes.
     */
    public function testWriteFile(): void
    {
        $content = random_bytes(64);

        $path = new Path($this->workDir, uniqid());
        FileSystem::writeFile($path, $content);

        $this->assertEquals(sha1($content), sha1_file($path));
    }
}
