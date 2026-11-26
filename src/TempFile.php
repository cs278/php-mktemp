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
    private string $path;
    private bool $released = false;

    /**
     * @param string|null $template
     * @param string|null $dir
     */
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
    public function getPath(): string
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
    public function open($mode = 'r+'): \SplFileObject
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
    public function keep($newPath): \SplFileInfo
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
    public function copyTo($stream): void
    {
        $this->assertNotReleased();

        stream_copy_to_stream($this->getStream(), $stream);
    }

    /**
     * Remove the temporary file.
     *
     * @return void
     */
    public function release(): void
    {
        if (!$this->released) {
            unlink($this->path);
            $this->released = true;
        }
    }

    private function assertNotReleased(): void
    {
        if ($this->released) {
            throw new \RuntimeException();
        }
    }

    /**
     * @param string $mode
     *
     * @return resource
     */
    private function getStream($mode = 'r')
    {
        $this->assertNotReleased();

        // @phpstan-ignore return.type (Needs fixing)
        return fopen($this->path, $mode);
    }
}
