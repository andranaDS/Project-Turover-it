<?php

namespace App\Core\Util;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Files
{
    public static function getUploadedFileFromAbsolutePath(string $absolutePath): ?UploadedFile
    {
        $filesystem = new Filesystem();

        try {
            $pathinfo = pathinfo($absolutePath);
            $filename = $pathinfo['filename'];
            $extension = $pathinfo['extension'] ?? null;
        } catch (\ErrorException $e) {
            return null;
        }

        $relativePath = '/tmp/' . $filename . '.' . $extension;

        if (false === $content = @file_get_contents($absolutePath, false, stream_context_create(['http' => ['timeout' => 1]]))) {
            return null;
        }

        try {
            $filesystem->dumpFile($relativePath, $content);
        } catch (IOException $e) {
            return null;
        }

        return self::getUploadedFileFromRelativePath($relativePath);
    }

    public static function getUploadedFileFromRelativePath(string $path): ?UploadedFile
    {
        $filesystem = new Filesystem();

        if (false === $filesystem->exists($path)) {
            return null;
        }

        try {
            $pathinfo = pathinfo($path);
            $basename = $pathinfo['basename'];
            $filename = $pathinfo['filename'];
            $extension = $pathinfo['extension'] ?? null;
        } catch (\ErrorException $e) {
            return null;
        }

        $tmpFilename = $filename . '-' . TokenGenerator::generate() . '.' . $extension;
        $tmpPath = '/tmp/' . $tmpFilename;

        try {
            $filesystem->copy($path, $tmpPath);
        } catch (IOExceptionInterface $e) {
            return null;
        }

        return new UploadedFile($tmpPath, $basename, null, null, true);
    }

    public static function getUploadedFile(string $path): ?UploadedFile
    {
        return self::getUploadedFileFromRelativePath($path);
    }

    public static function getCsvData(string $file, bool $ignoreFirstLine = false): array
    {
        $i = 0;
        $data = [];
        if (($handle = fopen($file, 'r')) !== false) {
            while (false !== ($d = fgetcsv($handle)) && \is_array($d)) {
                if (true === $ignoreFirstLine && 0 === $i++) {
                    continue;
                }
                $data[] = Arrays::map($d, function (string $e) {
                    return '' === $e ? null : trim($e);
                });
            }
            fclose($handle);
        }

        return $data;
    }
}
