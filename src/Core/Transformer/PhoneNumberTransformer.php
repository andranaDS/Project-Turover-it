<?php

namespace App\Core\Transformer;

use App\Core\Util\Strings;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberUtil;

class PhoneNumberTransformer
{
    public static function transform(?string $inValue): ?PhoneNumber
    {
        if (empty($inValue)) {
            return null;
        }

        $replaces = [
            '/\(0\)/' => '',
            '/^\+?330?/' => '+33',
            '/[^0-9+]/' => '',
        ];

        if (null === $inValue = preg_replace(array_keys($replaces), array_values($replaces), $inValue)) {
            return null;
        }

        if (Strings::startsWith($inValue, '+33')) {
            $inValue = substr($inValue, 0, 12);
        }

        $phoneNumberUtil = PhoneNumberUtil::getInstance();
        $regions = [PhoneNumberUtil::UNKNOWN_REGION, 'FR', 'CH', 'BE', 'LU', 'IT', 'EN', 'CA', 'US', 'MA', 'TN', 'DZ'];

        foreach ($regions as $region) {
            try {
                return $phoneNumberUtil->parse($inValue, $region);
            } catch (NumberParseException $exception) {
                continue;
            }
        }

        return null;
    }
}
