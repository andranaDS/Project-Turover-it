<?php

namespace App\Resource\Enum;

use Greg0ire\Enum\AbstractEnum;

final class FoundBy extends AbstractEnum
{
    public const FREEWORK = 'freework';
    public const DIRECTLY = 'directly';
    public const INTERMEDIARY = 'intermediary';
}
