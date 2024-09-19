<?php

namespace App\User\Validator;

use App\User\Entity\UserJob;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UserJobsValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UserJobs) {
            throw new UnexpectedTypeException($constraint, UserJobs::class);
        }

        if (!$value instanceof Collection) {
            throw new UnexpectedTypeException($constraint, Collection::class);
        }

        $mainJobsCount = $value->filter(static function (UserJob $userJob) {
            return true === $userJob->getMain();
        })->count();

        if ($mainJobsCount > $constraint->max) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}
