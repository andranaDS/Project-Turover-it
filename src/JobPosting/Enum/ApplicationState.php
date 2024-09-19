<?php

namespace App\JobPosting\Enum;

use Greg0ire\Enum\AbstractEnum;

final class ApplicationState extends AbstractEnum
{
    public const IN_PROGRESS = 'in_progress';
    public const UNSUCCESSFUL = 'unsuccessful';
    public const CANCELLED = 'cancelled';
    public const EXPIRED = 'expired';
}
