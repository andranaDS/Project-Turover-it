<?php

namespace App\JobPosting\Enum;

use Greg0ire\Enum\AbstractEnum;

final class Status extends AbstractEnum
{
    public const DRAFT = 'draft';
    public const PUBLISHED = 'published';
    public const PRIVATE = 'private';
    public const INACTIVE = 'inactive';
}
