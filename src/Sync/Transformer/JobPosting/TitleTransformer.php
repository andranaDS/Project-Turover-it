<?php

namespace App\Sync\Transformer\JobPosting;

use App\Core\Util\Arrays;

class TitleTransformer
{
    public static function transform(?string $inValue, ?string &$error = null): ?string
    {
        if (empty($inValue)) {
            $error = 'is empty';

            return null;
        }

        if (null === $outValue = preg_replace([
            sprintf('/\b(%s)\b/mi', implode('|', Arrays::map(['tres urgent', 'très urgent', 'urgent', 'TRÈS URGENT'], function (string $word) {
                return '\(?\w*' . addcslashes($word, '/') . '\w*\)?';
            }))),           // forbidden words
            '/\(\W*\)/',    // parentheses with empty word
            '/[\s\-_;]*$/', // end punctuation
            '/^[\s\-_;]*/', // start punctuation
            '/\s+/',        // multiple spaces
            '/^\s*$/',      // only spaces
        ], [
            '',
            '',
            '',
            '',
            ' ',
            '',
        ], $inValue)) {
            $error = 'is invalid';

            return null;
        }

        if (empty($outValue)) {
            $error = 'is empty';

            return null;
        }

        return $outValue;
    }
}
