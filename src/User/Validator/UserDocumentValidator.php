<?php

namespace App\User\Validator;

use App\User\Entity\UserDocument;
use App\User\Validator\UserDocument as UserDocumentConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UserDocumentValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UserDocumentConstraint) {
            throw new UnexpectedTypeException($constraint, UserDocumentConstraint::class);
        }

        if (!$value instanceof UserDocument) {
            throw new UnexpectedTypeException($value, UserDocument::class);
        }

        if (!$value->getContent() && !$value->getDocumentFile()) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}
