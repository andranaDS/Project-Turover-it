<?php

namespace App\User\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation()
 */
class UserDocument extends Constraint
{
    public string $message = 'generic.not_null';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
