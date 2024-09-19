<?php

namespace App\User\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation()
 */
class UserIdentity extends Constraint
{
    public string $message = 'user.identity';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
