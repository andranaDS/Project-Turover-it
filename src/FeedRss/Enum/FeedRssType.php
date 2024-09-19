<?php

namespace App\FeedRss\Enum;

use Greg0ire\Enum\AbstractEnum;

final class FeedRssType extends AbstractEnum
{
    public const CONTRACTOR = 'contractor';
    public const WORKER = 'worker';
    public const PREMIUM = 'premium';

    public static function isContractor(string $contract): bool
    {
        return self::CONTRACTOR === $contract;
    }
}
