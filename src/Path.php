<?php

namespace phplum\fs;

use Stringable;
use RuntimeException;

/**
 * Performs operations on file or directory path information with a
 * cross-platform manner.
 *
 * @property Path $parent
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
     * __get
     *
     * @param string $name Property name.
     *
     * @return Path Property value.
     * @throws RuntimeException If getting unknown properties.
     */
    public function __get(string $name)
    {
        return match ($name) {
            'parent' => new Path(...array_slice($this->segments, 0, (count($this->segments) - 1))),
            default => throw new RuntimeException('Getting unknown property: ' . get_class($this) . '::' . $name),
        };
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
