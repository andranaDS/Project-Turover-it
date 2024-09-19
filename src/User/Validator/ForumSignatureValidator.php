<?php

namespace App\User\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ForumSignatureValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ForumSignature) {
            throw new UnexpectedTypeException($constraint, ForumSignature::class);
        }

        if (null === $value) {
            return;
        }

        if (!\is_scalar($value) && !(\is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedValueException($value, 'string');
        }

        /** @var string $value */
        if (
            $constraint->maxLines && (substr_count($value, \PHP_EOL) > $constraint->maxLines - 1)
        ) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ maxLines }}', (string) $constraint->maxLines)
                ->addViolation()
            ;
        }
    }
}
