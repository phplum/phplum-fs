<?php

namespace phplum\fs;

use Stringable;

/**
 * Performs operations on file or directory path information with a
 * cross-platform manner.
 */
class Path implements Stringable
{
    /**
     * Segments of path parts.
     *
     * @var string[]
     */
    private array $segments;

    /**
     * __construct
     *
     * @param string ...$segments Segments of directory path to join with.
     */
    public function __construct(string ...$segments)
    {
        $this->segments = array_filter($segments);
    }

    /**
     * Returns a string representation according to current operating system.
     *
     * @return string Path as string.
     */
    public function __toString(): string
    {
        return join(DIRECTORY_SEPARATOR, $this->segments);
    }
}
