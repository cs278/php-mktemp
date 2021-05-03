<?php

namespace Cs278\Mktemp\Tests;

use Cs278\Mktemp;
use PHPUnit\Framework\TestCase;

final class TemporaryDirFunctionTest extends TestCase
{
    public function testCreateWithDefaults()
    {
        $path = Mktemp\temporaryDir();

        $this->assertRegExp('{^tmp\.[A-Za-z0-9]{6}$}', basename($path));
        $this->assertSame(sys_get_temp_dir(), \dirname($path));

        $this->assertTrue(is_dir($path));
        $this->assertTrue(is_readable($path));
        $this->assertTrue(is_writable($path));
        $this->assertTrue(is_executable($path));

        rmdir($path);
    }

    public function testCreateWithTemplate()
    {
        $path = Mktemp\temporaryDir('someXXXdir.XXX');

        $this->assertRegExp('{^someXXXdir\.[A-Za-z0-9]{3}$}', basename($path));
        $this->assertSame(sys_get_temp_dir(), \dirname($path));

        $this->assertTrue(is_dir($path));
        $this->assertTrue(is_readable($path));
        $this->assertTrue(is_writable($path));
        $this->assertTrue(is_executable($path));

        rmdir($path);
    }
}
