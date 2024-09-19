<?php

namespace App\User\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation()
 */
class UserJobSearchPreferences extends Constraint
{
    public string $messageBlank = 'generic.not_blank';
    public string $messageFreelanceOrEmployee = 'user.freelance_or_employee';
    public string $messageEnumMin = 'generic.enum.min';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
