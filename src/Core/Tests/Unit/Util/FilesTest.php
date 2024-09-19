<?php

namespace App\Core\Tests\Unit\Util;

use App\Core\Util\Files;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FilesTest extends TestCase
{
    public function testGetUploadedFileWithExistingFile(): void
    {
        $filesystem = new Filesystem();
        $filepath = '/tmp/good-path.txt';
        $filesystem->dumpFile($filepath, 'Hello world!');
        $uploadedFile = Files::getUploadedFile($filepath);
        self::assertNotNull($uploadedFile);
        self::assertInstanceOf(UploadedFile::class, $uploadedFile);
        self::assertMatchesRegularExpression('/\/tmp\/good-path-[a-zA-Z0-9]{16}\.txt/', $uploadedFile->getPathname());
        self::assertSame('txt', $uploadedFile->getExtension());
        self::assertSame('file', $uploadedFile->getType());
        self::assertTrue($uploadedFile->isFile());
        self::assertFalse($uploadedFile->isDir());
        self::assertTrue($uploadedFile->isReadable());
        self::assertFalse($uploadedFile->isExecutable());
    }

    public function testGetUploadedFileWithNonExistentFile(): void
    {
        self::assertNull(Files::getUploadedFile('/tmp/wrong-path.txt'));
    }
}
