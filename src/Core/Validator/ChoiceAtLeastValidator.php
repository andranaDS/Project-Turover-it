<?php

namespace App\Core\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ChoiceAtLeastValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ChoiceAtLeast) {
            throw new UnexpectedTypeException($constraint, ChoiceAtLeast::class);
        }

        if (\count(array_intersect($value, $constraint->choices)) >= $constraint->min) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->addViolation()
        ;
    }
}
