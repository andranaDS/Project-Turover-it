<?php

namespace App\Core\Validator;

use Createnl\ZxcvbnBundle\ZxcvbnFactoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class PasswordComplexityValidator extends ConstraintValidator
{
    private ZxcvbnFactoryInterface $zxcvbnFactory;

    public function __construct(ZxcvbnFactoryInterface $zxcvbnFactory)
    {
        $this->zxcvbnFactory = $zxcvbnFactory;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof PasswordComplexity) {
            throw new UnexpectedTypeException($constraint, PasswordComplexity::class);
        }

        if (null === $value) {
            return;
        }

        if (!\is_scalar($value) && !(\is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedValueException($value, 'string');
        }

        $zxcvbn = $this->zxcvbnFactory->createZxcvbn();

        /** @var string $value */
        if (
            $constraint->minScore && ($constraint->minScore > $zxcvbn->passwordStrength($value, [])['score'])
        ) {
            $this->context->buildViolation($constraint->message)
                ->setTranslationDomain('validators')
                ->addViolation()
            ;
        }
    }
}
