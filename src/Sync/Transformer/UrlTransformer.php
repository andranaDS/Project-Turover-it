<?php

namespace App\Sync\Transformer;

class UrlTransformer
{
    public static function transform(?string $inValue, ?string &$error = null): ?string
    {
        if (empty($inValue)) {
            return null;
        }

        $outValue = filter_var($inValue, \FILTER_VALIDATE_URL) ?: null;
        if (null !== $outValue) {
            return $outValue;
        }

        $outValue = 'http://' . $inValue;
        $outValue = filter_var($outValue, \FILTER_VALIDATE_URL) ?: null;
        if (null !== $outValue) {
            return $outValue;
        }

        $error = [];
        $error[] = sprintf('"%s" is not a valid url', $inValue);

        return null;
    }
}
