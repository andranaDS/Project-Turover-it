<?php

namespace App\User\Enum;

use Greg0ire\Enum\AbstractEnum;

final class LanguageLevel extends AbstractEnum
{
    public const NOTIONS = 'notions';
    public const LIMITED_PROFESSIONAL_SKILLS = 'limited_professional_skills';
    public const FULL_PROFESSIONAL_CAPACITY = 'full_professional_capacity';
    public const NATIVE_OR_BILINGUAL = 'native_or_bilingual';
}
