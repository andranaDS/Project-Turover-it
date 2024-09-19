<?php

namespace App\JobPosting\Enum;

use Greg0ire\Enum\AbstractEnum;

final class PublishedSince extends AbstractEnum
{
    public const LESS_THAN_24_HOURS = 'less_than_24_hours';
    public const FROM_1_TO_7_DAYS = 'from_1_to_7_days';
    public const FROM_8_TO_14_DAYS = 'from_8_to_14_days';
    public const FROM_15_DAYS_TO_1_MONTH = 'from_15_days_to_1_month';
}
