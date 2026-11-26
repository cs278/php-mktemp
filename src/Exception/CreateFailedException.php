<?php

namespace Cs278\Mktemp\Exception;

final class CreateFailedException extends \RuntimeException
{
    /** @param string $dir */
    public static function failedFile($dir): self
    {
        $dir = $dir
            ? rtrim($dir, DIRECTORY_SEPARATOR)
            : sprintf('cwd[%s]', getcwd());

        return new self(sprintf(
            'Failed to create temporary file in `%s`',
            $dir
        ));
    }

    /** @param string $dir */
    public static function failedDir($dir): self
    {
        $dir = $dir
            ? rtrim($dir, DIRECTORY_SEPARATOR)
            : sprintf('cwd[%s]', getcwd());

        return new self(sprintf(
            'Failed to create temporary directory in `%s`',
            $dir
        ));
    }
}
