<?php

namespace Cs278\Mktemp\Tests;

use Cs278\Mktemp;

class File extends \PHPUnit_Framework_TestCase
{
    public function testCreateWithDefaults()
    {
        $path = Mktemp\file();

        $this->assertRegExp('{^tmp\.[A-Za-z0-9]{6}$}', basename($path));
        $this->assertSame(sys_get_temp_dir(), dirname($path));

        $this->assertTrue(is_file($path));
        $this->assertTrue(is_readable($path));
        $this->assertTrue(is_writable($path));
        $this->assertFalse(is_executable($path));

        unlink($path);
    }

    public function testCreateWithTemplate()
    {
        $path = Mktemp\file('outXXXput.XXX.pdf');

        $this->assertRegExp('{^outXXXput\.[A-Za-z0-9]{3}\.pdf$}', basename($path));
        $this->assertSame(sys_get_temp_dir(), dirname($path));

        $this->assertTrue(is_file($path));
        $this->assertTrue(is_readable($path));
        $this->assertTrue(is_writable($path));
        $this->assertFalse(is_executable($path));

        unlink($path);
    }
}
