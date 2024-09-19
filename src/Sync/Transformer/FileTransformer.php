<?php

namespace App\Sync\Transformer;

class FileTransformer
{
    public static function transform(?string $inValue, string $prefix = '', ?string &$error = null): ?array
    {
        if (empty($inValue)) {
            return null;
        }

        $outValueUrl = $prefix . $inValue;
        $outValueSha1 = @sha1_file($outValueUrl);

        if (false === $outValueSha1) {
            $error = sprintf('"%s" was not found', $outValueUrl);

            return null;
        }

        return [
            'url' => $outValueUrl,
            'sha1' => $outValueSha1,
        ];
    }
}
