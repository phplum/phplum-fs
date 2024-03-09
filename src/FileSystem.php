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
     * @phpstan-ignore property.onlyWritten
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
}
