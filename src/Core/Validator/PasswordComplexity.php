<?php

namespace App\Core\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation()
 */
class PasswordComplexity extends Constraint
{
    public string $message = 'core.password_complexity';
    public int $minScore = 2;

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
