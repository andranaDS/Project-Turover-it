<?php

namespace App\Resource\Enum;

use Greg0ire\Enum\AbstractEnum;

final class Employer extends AbstractEnum
{
    public const FINAL_CLIENT = 'final_client';
    public const DIGITAL_SERVICE_COMPANY = 'digital_service_company';
    public const AGENCY = 'agency';
    public const RECRUITMENT_AGENCY = 'recruitment_agency';

    public static function getNonFinalClientValues(): array
    {
        return [self::DIGITAL_SERVICE_COMPANY, self::AGENCY, self::RECRUITMENT_AGENCY];
    }

    public static function getFinalClientValues(): array
    {
        return [self::FINAL_CLIENT];
    }
}
