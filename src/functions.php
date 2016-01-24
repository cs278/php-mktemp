<?php

namespace Cs278\Mktemp;

use Cs278\Mktemp\Exception\CreateFailedException;

/**
 * Create a temporary file and return the path.
 *
 * Files are created with mode u+rw minus umask.
 *
 * @param string|null $template Filename template
 * @param string|null $dir      Directory to place file, system temporary
 *                              directory iff null
 *
 * @return string Path to temporary file
 */
function temporaryFile($template = null, $dir = null)
{
    $template = $template ?: 'tmp.XXXXXX';
    $dir = $dir !== null ? $dir : sys_get_temp_dir();
    $dir = $dir ? $dir.'/' : '';
    $attempts = 5;

    do {
        $path = $dir.pathTemplate($template);

        if (false !== $handle = @fopen($path, 'xb')) {
            if ('\\' !== \DIRECTORY_SEPARATOR) {
                @chmod(0600 & ~umask());
            }

            fclose($handle);

            return $path;
        }
    } while (0 < --$attempts);

    throw CreateFailedException::failedFile($dir);
}

/**
 * Create a temporary directory and return the path.
 *
 * Directories are created with mode u+rwx minus umask.
 *
 * @param string|null $template Directory template
 * @param string|null $dir      Directory to place created directory, system
 *                              temporary directory iff null
 *
 * @return string Path to temporary directory
 */
function temporaryDir($template = null, $dir = null)
{
    $template = $template ?: 'tmp.XXXXXX';
    $dir = $dir !== null ? $dir : sys_get_temp_dir();
    $dir = $dir ? $dir.'/' : '';
    $attempts = 5;

    do {
        $path = $dir.pathTemplate($template);

        // umask is applied by PHP
        if (false !== @mkdir($path, 0700)) {
            return $path;
        }
    } while (0 < --$attempts);

    throw CreateFailedException::failedDir($dir);
}

/**
 * @internal
 */
function pathTemplate($template)
{
    static $alphabet;
    static $useRandomInt;

    if (!$alphabet) {
        $alphabet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $useRandomInt = function_exists('random_int');
    }

    $len = 0;

    if (false !== $pos = strrpos($template, 'X')) {
        $len = 1;

        while ($pos > 0 && 'X' === $template[$pos - 1]) {
            --$pos;
            ++$len;
        }
    }

    if (3 > $len) {
        throw new \InvalidArgumentException(sprintf(
            'Too few X\'s in template `%s`',
            $template
        ));
    }

    $replacement = '';

    for ($i = 0; $i < $len; ++$i) {
        $rand = $useRandomInt
            ? random_int(0, 61)
            : mt_rand(0, 61);

        $replacement .= $alphabet[$rand];
    }

    return substr_replace($template, $replacement, $pos, $len);
}
