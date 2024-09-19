<?php

namespace App\User\Enum;

use Greg0ire\Enum\AbstractEnum;

final class ExperienceYear extends AbstractEnum
{
    public const LESS_THAN_1_YEAR = 'less_than_1_year';
    public const YEARS_1_2 = '1-2_years';
    public const YEARS_3_4 = '3-4_years';
    public const YEARS_5_10 = '5-10_years';
    public const YEARS_11_15 = '11-15_years';
    public const MORE_THAN_15_YEARS = 'more_than_15_years';
}
