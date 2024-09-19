<?php

namespace App\User\Validator;

use App\User\Entity\UserSkill;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UserSkillsValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UserSkills) {
            throw new UnexpectedTypeException($constraint, UserSkills::class);
        }

        if (!$value instanceof Collection) {
            throw new UnexpectedTypeException($constraint, Collection::class);
        }

        $mainSkillsCount = $value->filter(static function (UserSkill $userSkill) {
            return true === $userSkill->getMain();
        })->count();

        if ($mainSkillsCount > $constraint->max) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}
