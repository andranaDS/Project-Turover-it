<?php

namespace App\Sync\Transformer;

class TimestampTransformer
{
    public static function transform(?string $inValue, ?string &$error = null): ?\DateTime
    {
        if (empty($inValue)) {
            return null;
        }

        if (true === ctype_digit($inValue)) {
            return (new \DateTime())->setTimestamp((int) $inValue);
        }

        $error = [];
        $error[] = sprintf('"%s" is not a valid timestamp', $inValue);

        return null;
    }
}
