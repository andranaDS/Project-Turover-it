<?php

namespace App\Sync\Transformer;

class MatchTransformer
{
    // @phpstan-ignore-next-line
    public static function transform(?string $inValue, array $matches = [], ?string &$error = null)
    {
        if (null === $inValue || '' === $inValue) {
            return null;
        }

        if (null !== ($outValue = $matches[$inValue] ?? null)) {
            return $outValue;
        }

        $error = sprintf('No match was found for "%s". Possible values are ["%s"]', $inValue, implode('", "', array_keys($matches)));

        return null;
    }
}
