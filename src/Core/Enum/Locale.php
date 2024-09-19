<?php

namespace App\Core\Enum;

use Greg0ire\Enum\AbstractEnum;

final class Locale extends AbstractEnum
{
    // France
    public const fr_FR = 'fr_FR';

    // Switzerland
    public const it_CH = 'it_CH';
    public const de_CH = 'de_CH';
    public const fr_CH = 'fr_CH';

    // Luxembourg
    public const fr_LU = 'fr_LU';
    public const de_LU = 'de_LU';

    // Belgium
    public const fr_BE = 'fr_BE';
    public const nl_BE = 'nl_BE';

    // Great Britain
    public const en_GB = 'en_GB';

    public static function getBoChoices(): array
    {
        $values = array_values(self::getConstants());

        return array_combine($values, $values);
    }
}
