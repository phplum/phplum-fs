<?php

namespace phplum\fs;

use Throwable;

/**
 * The exception that is thrown when an attempt to access a file or directory that does not exist on disk fails.
 */
class FileNotFoundException extends IOException
{
    /**
     * __construct
     *
     * @param string $path File path.
     * @param integer $code Error code.
     * @param Throwable|null $previous Previous exception.
     */
    public function __construct(string $path, int $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('No such file or directory: "%s".', $path), $code, $previous);
    }
}
