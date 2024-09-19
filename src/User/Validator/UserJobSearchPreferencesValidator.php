<?php

namespace App\User\Validator;

use App\User\Entity\User;
use App\User\Enum\FreelanceLegalStatus;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UserJobSearchPreferencesValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UserJobSearchPreferences) {
            throw new UnexpectedTypeException($constraint, UserJobSearchPreferences::class);
        }

        if (!$value instanceof User) {
            throw new UnexpectedTypeException($value, User::class);
        }

        if (false === $value->getFreelance() && false === $value->getEmployee()) {
            $this->context->buildViolation($constraint->messageFreelanceOrEmployee)
                ->addViolation()
            ;
        }

        if (true === $value->getFreelance()) {
            if (FreelanceLegalStatus::UMBRELLA_COMPANY === $value->getFreelanceLegalStatus()) {
                if (null === $value->getUmbrellaCompany()) {
                    $this->context->buildViolation($constraint->messageBlank)
                        ->atPath('umbrellaCompany')
                        ->addViolation()
                    ;
                }
            }
            if (
                FreelanceLegalStatus::UMBRELLA_COMPANY === $value->getFreelanceLegalStatus()
                || (
                    \in_array($value->getFreelanceLegalStatus(), FreelanceLegalStatus::getSelfEmployedStatus(), true)
                    && false === $value->getCompanyRegistrationNumberBeingAttributed()
                )
            ) {
                if (empty($value->getCompanyRegistrationNumber())) {
                    $this->context->buildViolation($constraint->messageBlank)
                        ->atPath('companyRegistrationNumber')
                        ->addViolation()
                    ;
                }

                if (empty($value->getCompanyCountryCode())) {
                    $this->context->buildViolation($constraint->messageBlank)
                        ->atPath('companyCountryCode')
                        ->addViolation()
                    ;
                }
            }
        }

        if (true === $value->getEmployee()) {
            if (empty($value->getContracts())) {
                $this->context->buildViolation($constraint->messageEnumMin)
                    ->setParameter('{{ limit }}', '1')
                    ->atPath('contracts')
                    ->setPlural(1)
                    ->addViolation()
                ;
            }
        }
    }
}
