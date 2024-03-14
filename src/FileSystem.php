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

    /**
     * Removes a file or symbolic link.
     *
     * @param string $path Path of file.
     *
     * @return void
     * @throws FileNotFoundException If the specified file is missing.
     * @throws IOException If failed to remove the file.
     */
    public static function unlink(string $path): void
    {
        if (!file_exists($path)) {
            throw new FileNotFoundException($path);
        }

        if (!self::invoke('unlink', $path)) {
            throw new IOException(sprintf('Failed to remove file: "%s": %s.', $path, self::$lastError));
        }
    }

    /**
     * Removes files and directories.
     *
     * @param string $path Path of file or directory.
     * @param boolean $recursive If true, perform a recursive directory removal.
     * @param boolean $force When true, exceptions will be ignored if $path does not exist.
     *
     * @return void
     * @throws FileNotFoundException If the specified file is missing.
     * @throws IOException If failed to remove the file.
     */
    public static function rm(string $path, bool $recursive = false, bool $force = false): void
    {
        if (!file_exists($path)) {
            if ($force) {
                return;
            }

            throw new FileNotFoundException($path);
        }

        if (is_file($path)) {
            self::unlink($path);
            return;
        }

        if (!$recursive) {
            throw new IOException(sprintf('Failed to remove: "%s" is a directory.', $path));
        }

        $items = glob(new Path($path, '{,.}*'), (GLOB_MARK | GLOB_BRACE));

        if ($items === false) {
            throw new IOException(sprintf('Error occurred on finding files in "%s".', $path));
        }

        foreach ($items as $item) {
            if (basename($item) == '.' || basename($item) == '..') {
                continue;
            }

            self::rm($item, $recursive, $force);
        }

        if (!self::invoke('rmdir', $path)) {
            throw new IOException(sprintf('Failed to remove: "%s": %s', $path, self::$lastError));
        }
    }

    /**
     * Writes data to a file, replacing the file if it already exists.
     *
     * @param string|resource $file Path or pointer resource of file.
     * @param mixed $data Data to be written.
     * @param string $mode File mode, only affects the newly created file.
     *
     * @return void
     * @throws IOException If error occurred on writing file.
     */
    public static function writeFile(mixed $file, mixed $data, string $mode = 'w'): void
    {
        if (!is_resource($file)) {
            $fp = self::invoke('fopen', $file, $mode);

            if ($fp === false) {
                throw new IOException(sprintf('Failed to open file: %s.', self::$lastError));
            }
        } else {
            $fp = $file;
        }

        if (self::invoke('fwrite', $fp, $data) === false) {
            throw new IOException(sprintf('Failed to write file: %s.', self::$lastError));
        }

        if (self::invoke('fclose', $fp) === false) {
            throw new IOException(sprintf('Failed to close file: %s.', self::$lastError));
        }
    }

    /**
     * Copies the source file to destination.
     *
     * @param string $src Path of source file.
     * @param string $dest Path of destination.
     *
     * @return void
     * @throws IOException If failed to copy file.
     */
    public static function copyFile(string $src, string $dest): void
    {
        if (self::invoke('copy', $src, $dest) === false) {
            throw new IOException(sprintf('Failed to copy file "%s" to "%s": %s', $src, $dest, self::$lastError));
        }
    }

    /**
     * Copies the source file or entire directory to destination.
     *
     * If $recursive = false, this method works similarly with copyFile.
     *
     * @param string $src Path of source file or directory.
     * @param string $dest Path of destination.
     *
     * @return void
     * @throws IOException If failed to copy files.
     */
    public static function cp(string $src, string $dest): void
    {
        if (is_file($src)) {
            self::copyFile($src, $dest);
            return;
        }

        $mode = fileperms($src);

        if ($mode === false) {
            $mode = 0777;
        }

        self::mkdir($dest, $mode);

        $dir = opendir($src);

        if ($dir === false) {
            throw new IOException(sprintf('Unable to open directory "%s".', $src));
        }

        try {
            while (($file = readdir($dir)) !== false) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                self::cp(new Path($src, $file), new Path($dest, $file));
            }
        } finally {
            closedir($dir);
        }
    }

    /**
     * Removes a directory.
     *
     * @param string $path Path of directory.
     *
     * @return void
     * @throws IOException If failed to remove directory.
     */
    public static function rmdir(string $path): void
    {
        if (!file_exists($path)) {
            return;
        }

        if (self::invoke('rmdir', $path) === false) {
            throw new IOException(sprintf('Failed to remove directory "%s": %s', $path, self::$lastError));
        }
    }
}
