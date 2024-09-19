<?php

namespace App\Core\Util;

class ContentDetector
{
    public static function detect(string $value, array $contentsToDetect): array
    {
        $value = Strings::stripTags($value);

        $detectedContents = [];

        $matches = [];
        $count = preg_match_all('/\b(' . implode('|', $contentsToDetect) . ')\b/miu', $value, $matches);

        if ($count > 0) {
            $detectedContents = array_unique(Arrays::map($matches[0], static function (string $content) {
                return mb_strtolower($content, 'utf8');
            }));
            sort($detectedContents);
        }

        return $detectedContents;
    }
}
