<?php

namespace App\User\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation()
 */
class UserEmail extends Constraint
{
    public string $message = 'generic.email';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
