<?php

namespace phplum\fs;

use RuntimeException;

/**
 * Enables interacting with the file system in a way modeled on standard
 * POSIX utilities.
 */
final class FileSystem
{
    /**
     * Last error for `invoke` method.
     *
     * @var string|null
     */
    private static ?string $lastError;

    /**
     * Invokes the specified function with error handling.
     *
     * @param string $fn Function to invoke.
     * @param mixed ...$args Arguments to invoke with.
     *
     * @return mixed Return value of function.
     * @throws RuntimeException If function not defined.
     */
    private static function invoke(string $fn, mixed ...$args): mixed
    {
        if (!function_exists($fn)) {
            throw new RuntimeException(sprintf('Function "%s" not defined.', $fn));
        }

        self::$lastError = null;

        // @phpstan-ignore-next-line
        set_error_handler(callback: __CLASS__ . '::errorHandler');

        try {
            return $fn(...$args);
        } finally {
            restore_error_handler();
        }
    }

    /**
     * Error handler for `invoke` method.
     *
     * @param integer $type Error type.
     * @param string $msg Error message.
     *
     * @return void
     * @internal
     */
    public static function errorHandler(int $type, string $msg): void
    {
        self::$lastError = $msg;
    }

    /**
     * Creates a new directory.
     *
     * @param string $path Path of the new directory.
     * @param integer $mode Access permissions of the new directory.
     * @param boolean $recursive If true, parent directories will also be created.
     *
     * @return void
     * @throws IOException If failed to create the directory.
     */
    public static function mkdir(string $path, int $mode = 0777, bool $recursive = false): void
    {
        if (is_dir($path)) {
            return;
        }

        if (!self::invoke('mkdir', $path, $mode, $recursive)) {
            throw new IOException(sprintf('Failed to create directory: "%s": %s', $path, self::$lastError));
        }
    }
}
