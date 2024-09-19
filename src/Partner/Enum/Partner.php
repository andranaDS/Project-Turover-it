<?php

namespace App\Partner\Enum;

use Greg0ire\Enum\AbstractEnum;

final class Partner extends AbstractEnum
{
    public const NONE = 'none';
    public const FREELANCECOM = 'freelancecom';

    public static function getPartners(): array
    {
        return [
            self::FREELANCECOM,
        ];
    }
}
