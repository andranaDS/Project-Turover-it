<?php

namespace App\Folder\Enum;

use Greg0ire\Enum\AbstractEnum;

class FolderType extends AbstractEnum
{
    public const VIEWED = 'viewed';
    public const CART = 'cart';
    public const YESTERDAY_CART = 'yesterday_cart';
    public const FAVORITES = 'favorites';
    public const COMMENTED = 'commented';
    public const EMAILING = 'emailing';
    public const HIDDEN = 'hidden';
    public const PERSONAL = 'personal';

    public static function getMandatoryTypes(): array
    {
        return [
            self::VIEWED,
            self::CART,
            self::YESTERDAY_CART,
            self::FAVORITES,
            self::COMMENTED,
            self::EMAILING,
            self::HIDDEN,
        ];
    }
}
