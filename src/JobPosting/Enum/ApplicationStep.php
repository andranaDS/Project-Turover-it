<?php

namespace App\JobPosting\Enum;

use Greg0ire\Enum\AbstractEnum;

final class ApplicationStep extends AbstractEnum
{
    public const RESUME = 'resume';
    public const SEEN = 'seen';
    public const KO = 'ko';
    public const CANCELLED = 'cancelled';
}
