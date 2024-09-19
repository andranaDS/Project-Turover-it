<?php

namespace App\Core\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation()
 */
class ChoiceAtLeast extends Constraint
{
    public string $min;
    public array $choices;
    public string $message = 'core.choice_at_least.invalid';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
