<?php

namespace App\Core\Validator;

use App\Core\Entity\Location;
use App\Core\Validator\Location as LocationConstraint;
use App\User\Enum\CompanyCountryCode;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class LocationValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof LocationConstraint) {
            throw new UnexpectedTypeException($constraint, LocationConstraint::class);
        }

        if (!$value instanceof Location) {
            throw new UnexpectedTypeException($value, Location::class);
        }

        if (!empty($value->getCountryCode()) && (
            CompanyCountryCode::FR !== $value->getCountryCode() ||
            null !== $value->getLocality() ||
            null !== $value->getAdminLevel2() ||
            null !== $value->getAdminLevel1()
        )) {
            return;
        }
        $this->context->buildViolation($constraint->message)
            ->addViolation()
        ;
    }
}
