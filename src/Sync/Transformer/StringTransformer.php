<?php

namespace App\Sync\Transformer;

use App\Core\Util\Strings;

class StringTransformer
{
    public static function transform(?string $inValue, int $maxLength = null, bool $required = false, ?string &$error = null): ?string
    {
        if (empty($inValue)) {
            if (true === $required) {
                $error = 'required';
            }

            return null;
        }

        if (null === $maxLength) {
            return $inValue;
        }

        if ((null !== $outValue = preg_replace('!\s+!', ' ', $inValue))
            && (null !== $outValue = preg_replace('/\s/', ' ', $outValue))) {
            return Strings::substrToLength($outValue, $maxLength);
        }

        $error = sprintf('"%s" is not a valid string', $inValue);

        return null;
    }
}
