<?php

namespace App\Sync\Transformer;

class DateTimeTransformer
{
    public static function transform(?string $inValue, ?string &$error = null): ?\DateTime
    {
        if (empty($inValue)) {
            return null;
        }

        if (false !== $outValue = \DateTime::createFromFormat('Y-m-d H:i:s', $inValue)) {
            return $outValue;
        }

        $error = sprintf('"%s" is not a valid datetime', $inValue);

        return null;
    }
}
