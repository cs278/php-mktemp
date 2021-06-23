<?php

namespace Cs278\Mktemp;

/**
 * Temporary file class.
 *
 * Handles the life cycle of a temporary file, automatically cleaning up when the
 * object goes out of scope.
 */
final class TempFile
{
    private $path;
    private $released = false;

    public function __construct($template = null, $dir = null)
    {
        $this->path = temporaryFile($template, $dir);
    }

    public function __destruct()
    {
        $this->release();
    }

    /**
     * Fetch the disk location of the temporary file.
     *
     * Use this for interacting with the file outside of PHP.
     *
     * @return string
     */
    public function getPath()
    {
        $this->assertNotReleased();

        return $this->path;
    }

    /**
     * Create \SplFileObject for access to file contents.
     *
     * @param string $mode Stream access mode
     *
     * @return \SplFileObject
     */
    public function open($mode = 'r+')
    {
        $this->assertNotReleased();

        return new \SplFileObject($this->path, $mode, false);
    }

    /**
     * Store the temporary file permanently at a given path.
     *
     * Attempts to perform a rename and falls back to a copy.
     *
     * @param string $newPath
     *
     * @return \SplFileInfo Represents the new file.
     */
    public function keep($newPath)
    {
        $this->assertNotReleased();

        $moved = false;

        if (stream_is_local($newPath)) {
            $moved = rename($this->path, $newPath);
        }

        if (!$moved) {
            copy($this->path, $newPath);
            unlink($this->path);
        }

        $this->released = true;

        return new \SplFileInfo($newPath);
    }

    /**
     * Copy the contents of the file into a supplied stream.
     *
     * @param resource $stream
     *
     * @return void
     */
    public function copyTo($stream)
    {
        $this->assertNotReleased();

        stream_copy_to_stream($this->getStream(), $stream);
    }

    /**
     * Remove the temporary file.
     *
     * @return void
     */
    public function release()
    {
        if (!$this->released) {
            if (\PHP_VERSION_ID < 70300 && \DIRECTORY_SEPARATOR === '\\') {
                // Handle Windows pre PHP 7.3.x which cannot handle unlinking paths
                // where the file handle is still in use. I believe this can happen
                // because PHP does not necessarily garbage collect objects in the
                // correct order. So sometimes on Windows and PHP 7.2 the unlink
                // can fail, this works around that by delaying the unlink until
                // PHP shuts down.
                // https://www.php.net/manual/en/function.unlink.php#refsect1-function.unlink-changelog
               $result = @unlink($this->path);

                if (!$result) {
                    $path = $this->path;

                    \register_shutdown_function(function () use ($path) {
                        @unlink($path);
                    });
                }
            } else {
                unlink($this->path);
            }
            $this->released = true;
        }
    }

    private function assertNotReleased()
    {
        if ($this->released) {
            throw new \RuntimeException();
        }
    }

    private function getStream($mode = 'r')
    {
        $this->assertNotReleased();

        return fopen($this->path, $mode);
    }
}
