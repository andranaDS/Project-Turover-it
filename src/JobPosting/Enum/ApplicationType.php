<?php

namespace App\JobPosting\Enum;

use Greg0ire\Enum\AbstractEnum;

final class ApplicationType extends AbstractEnum
{
    public const TURNOVER = 'turnover';
    public const CONTACT = 'contact';
    public const URL = 'url';
}
