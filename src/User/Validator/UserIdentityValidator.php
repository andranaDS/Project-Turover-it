<?php

namespace App\User\Validator;

use App\User\Entity\User;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UserIdentityValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UserIdentity) {
            throw new UnexpectedTypeException($constraint, UserIdentity::class);
        }

        if (!$value instanceof User) {
            throw new UnexpectedTypeException($constraint, User::class);
        }

        if (false === $value->getEnabled()) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('email')
                ->addViolation()
            ;
        }
    }
}
