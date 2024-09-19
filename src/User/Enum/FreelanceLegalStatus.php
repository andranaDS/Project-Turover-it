<?php

namespace App\User\Enum;

use Greg0ire\Enum\AbstractEnum;

final class FreelanceLegalStatus extends AbstractEnum
{
    public const SELF_EMPLOYED = 'self_employed';
    public const SAS_SASU = 'sas_sasu';
    public const SARL_EURL = 'sarl_eurl';
    public const LIBERAL_PROFESSION = 'liberal_profession';
    public const UMBRELLA_COMPANY = 'umbrella_company';
    public const STATUS_IN_PROGRESS = 'status_in_progress';

    public static function getSelfEmployedStatus(): array
    {
        return [self::SELF_EMPLOYED, self::SAS_SASU, self::SARL_EURL, self::LIBERAL_PROFESSION];
    }
}
