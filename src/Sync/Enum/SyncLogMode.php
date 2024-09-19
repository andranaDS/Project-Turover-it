<?php

namespace App\Sync\Enum;

use Greg0ire\Enum\AbstractEnum;

final class SyncLogMode extends AbstractEnum
{
    public const CREATE = 'create';
    public const UPDATE = 'update';
    public const SKIP = 'skip';
    public const FAIL = 'fail';
    public const UNPUBLISH = 'unpublish';
}
