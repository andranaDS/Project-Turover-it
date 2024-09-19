<?php

namespace App\Core\Validator;

use App\Core\Spam\ForbiddenContentDetector;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ForbiddenContentValidator extends ConstraintValidator
{
    private ForbiddenContentDetector $detector;

    public function __construct(ForbiddenContentDetector $detector)
    {
        $this->detector = $detector;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ForbiddenContent) {
            throw new UnexpectedTypeException($constraint, ForbiddenContent::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $contents = [];

        if (false === $this->detector->isForbiddenValue($value, $contents)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ content }}', '"' . implode('", "', $contents) . '"')
            ->addViolation()
        ;
    }
}
