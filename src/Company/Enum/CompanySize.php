<?php

namespace App\Company\Enum;

use Greg0ire\Enum\AbstractEnum;

final class CompanySize extends AbstractEnum
{
    public const LESS_THAN_20_EMPLOYEES = 'less_than_20_employees';
    public const EMPLOYEES_20_99 = 'employees_20_99';
    public const EMPLOYEES_100_249 = 'employees_100_249';
    public const EMPLOYEES_250_999 = 'employees_250_999';
    public const MORE_THAN_1000_EMPLOYEES = 'more_than_1000_employees';
}
