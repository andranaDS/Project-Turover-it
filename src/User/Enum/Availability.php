<?php

namespace App\User\Enum;

use Greg0ire\Enum\AbstractEnum;

final class Availability extends AbstractEnum
{
    public const IMMEDIATE = 'immediate';
    public const WITHIN_1_MONTH = 'within_1_month';
    public const WITHIN_2_MONTH = 'within_2_month';
    public const WITHIN_3_MONTH = 'within_3_month';
    public const DATE = 'date';
    public const NONE = 'none';

    public static function getRelativeValues(): array
    {
        return [self::IMMEDIATE, self::WITHIN_1_MONTH, self::WITHIN_2_MONTH, self::WITHIN_3_MONTH];
    }

    public static function isRelative(string $availability): bool
    {
        return \in_array($availability, self::getRelativeValues(), true);
    }
}
