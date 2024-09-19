<?php

namespace App\Sync\Transformer\JobPosting;

use App\Core\Util\Strings;

class RenewableTransformer
{
    public static function transform(?string $inValue): bool
    {
        if (empty($inValue)) {
            return false;
        }

        $inValue = mb_strtolower($inValue, 'utf8');

        $matches = [
            'renew',
            'recond',
            'renouv',
            'prolon',
            'extension',
            'minimum',
            '+',
            '>',
        ];

        foreach ($matches as $match) {
            if (Strings::contains($inValue, $match)) {
                return true;
            }
        }

        return false;
    }
}
