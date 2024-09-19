<?php

namespace App\User\Enum;

use Greg0ire\Enum\AbstractEnum;

final class UserProfileStep extends AbstractEnum
{
    public const UPLOAD_RESUME = 'upload_resume';
    public const PERSONAL_INFO = 'personal_info';
    public const JOB_SEARCH_PREFERENCES = 'job_search_preferences';
    public const SKILLS_AND_LANGUAGES = 'skills_and_languages';
    public const EDUCATION = 'education';
    public const STATUS = 'status';
    public const ABOUT_ME = 'about_me';

    public static function getMandatoriesSteps(): array
    {
        return [self::UPLOAD_RESUME, self::PERSONAL_INFO, self::JOB_SEARCH_PREFERENCES, self::SKILLS_AND_LANGUAGES, self::EDUCATION, self::STATUS];
    }

    public static function getOptionnalsSteps(): array
    {
        return [self::ABOUT_ME];
    }
}
