<?php

namespace App\Core\Validator;

use App\Core\Entity\Location;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class LocationNotNullValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof LocationNotNull) {
            throw new UnexpectedTypeException($constraint, LocationNotNull::class);
        }

        if (null === $value || !$value instanceof Location) {
            return;
        }

        if (
            null !== $value->getLongitude()
            && null !== $value->getLatitude()
            && null !== $value->getCountry()
        ) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->addViolation()
        ;
    }
}
