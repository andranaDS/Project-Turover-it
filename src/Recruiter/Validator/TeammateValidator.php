<?php

namespace App\Recruiter\Validator;

use App\Recruiter\Entity\Recruiter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class TeammateValidator extends ConstraintValidator
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Teammate) {
            throw new UnexpectedTypeException($constraint, Teammate::class);
        }

        if (!$value instanceof Recruiter) {
            throw new UnexpectedTypeException($value, Recruiter::class);
        }

        /** @var Recruiter $recruiter */
        $recruiter = $this->security->getUser();

        if ($recruiter->getCompany() === $value->getCompany()) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->addViolation()
        ;
    }
}
