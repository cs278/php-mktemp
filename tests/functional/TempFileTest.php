<?php

namespace Cs278\Mktemp\Tests;

use Cs278\Mktemp\TempFile;
use Cs278\Mktemp;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\SetUpTearDownTrait;

final class TempFileTest extends TestCase
{
    use SetUpTearDownTrait;

    private $tmpdir;

    public function doSetUp()
    {
        $this->tmpdir = Mktemp\temporaryDir();
    }

    public function doTearDown()
    {
        foreach (glob($this->tmpdir.'/*') as $file) {
            unlink($file);
        }

        rmdir($this->tmpdir);
    }

    public function testConstructor()
    {
        $file = $this->createTempFile();
        $path = $file->open('r')->getRealPath();

        $this->assertFileExists($path);
        $this->assertTrue(is_file($path));
        $this->assertSame(0, filesize($path));
    }

    public function testDestructor()
    {
        $file = $this->createTempFile();
        $path = $file->open('r')->getRealPath();
        $this->assertFileExists($path);

        $file = null;

        $this->assertFileNotExists($path);
    }

    public function testGetPath()
    {
        $file = $this->createTempFile();

        $this->assertFileExists($file->getPath());
        $this->assertStringStartsWith($this->tmpdir, $file->getPath());
    }

    public function testOpen()
    {
        $file = $this->createTempFile();
        $obj = $file->open();

        $this->assertInstanceOf('\\SplFileObject', $obj);

        $obj->fwrite('foobar');
        $obj->fseek(0);
        $this->assertSame('foobar', $obj->fgets());
    }

    public function testKeep()
    {
        $newPath = $this->tmpdir.'/important-file';

        $this->assertFileNotExists($newPath);

        $file = $this->createTempFile();
        $oldPath = $file->open()->getRealPath();
        $file->open()->fwrite('Hello World!');

        $this->assertInstanceOf('\\SplFileInfo', $file->keep($newPath));

        $this->assertFileNotExists($oldPath);
        $this->assertFileExists($newPath);
        $this->assertSame('Hello World!', file_get_contents($newPath));
    }

    public function testCopyTo()
    {
        $file = $this->createTempFile();
        $file->open()->fwrite('Hello World!');

        $targetStream = fopen('php://memory', 'r+');

        $this->assertNull($file->copyTo($targetStream));

        fseek($targetStream, 0);

        $this->assertFileExists($file->open()->getRealPath());
        $this->assertSame('Hello World!', stream_get_contents($targetStream));
    }

    public function testRelease()
    {
        $file = $this->createTempFile();
        $path = $file->open('r')->getRealPath();
        $this->assertFileExists($path);

        $this->assertNull($file->release());

        $this->assertFileNotExists($path);

        $this->assertNull($file->release());
    }

    private function createTempFile($template = null)
    {
        return new TempFile($template, $this->tmpdir);
    }
}
