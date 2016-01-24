<?php

namespace Cs278\Mktemp\Exception;

final class CreateFailedException extends \RuntimeException
{
    public static function failedFile($dir)
    {
        $dir = $dir
            ? rtrim($dir, DIRECTORY_SEPARATOR)
            : sprintf('cwd[%s]', getcwd());

        return new self(sprintf(
            'Failed to create temporary file in `%s`',
            $dir
        ));
    }

    public static function failedDir($dir)
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
