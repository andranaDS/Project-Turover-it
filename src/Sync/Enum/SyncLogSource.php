<?php

namespace App\Sync\Enum;

use Greg0ire\Enum\AbstractEnum;

final class SyncLogSource extends AbstractEnum
{
    public const CRON = 'cron';
    public const MANUAL = 'manual';
}
