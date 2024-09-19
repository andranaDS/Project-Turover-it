<?php

namespace App\JobPosting\Enum;

use Greg0ire\Enum\AbstractEnum;

final class RemoteMode extends AbstractEnum
{
    public const NONE = 'none';
    public const PARTIAL = 'partial';
    public const FULL = 'full';
}
