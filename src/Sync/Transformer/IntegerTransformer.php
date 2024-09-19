<?php

namespace App\Sync\Transformer;

class IntegerTransformer
{
    public static function transform(?string $inValue, ?int $default = null, ?string &$error = null): ?int
    {
        if (empty($inValue)) {
            return $default;
        }

        if (true === ctype_digit($inValue)) {
            return (int) $inValue;
        }

        $error = [];
        $error[] = sprintf('"%s" is not a valid int', $inValue);

        return $default;
    }
}
