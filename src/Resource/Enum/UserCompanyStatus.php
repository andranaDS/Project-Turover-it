<?php

namespace App\Resource\Enum;

use Greg0ire\Enum\AbstractEnum;

final class UserCompanyStatus extends AbstractEnum
{
    public const COMPANY = 'company';
    public const MICRO_COMPANY = 'micro_company';
    public const SALARY_PORTAGE = 'salary_portage';
}
