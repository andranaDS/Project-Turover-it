<?php

namespace App\User\Validator;

use Egulias\EmailValidator\EmailLexer;
use Egulias\EmailValidator\EmailParser;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class UserEmailValidator extends ConstraintValidator
{
    private EmailLexer $lexer;

    public function __construct()
    {
        $this->lexer = new EmailLexer();
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UserEmail) {
            throw new UnexpectedTypeException($constraint, UserEmail::class);
        }

        if (!\is_scalar($value) && !(\is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedValueException($value, 'string');
        }

        $parser = new EmailParser($this->lexer);
        try {
            $result = $parser->parse((string) $value);

            if ($result->isInvalid()) {
                $this->buildViolation($constraint);
            }
        } catch (\Exception $invalid) {
            $this->buildViolation($constraint);
        }
    }

    private function buildViolation(UserEmail $constraint): void
    {
        $this->context
            ->buildViolation($constraint->message)
            ->addViolation()
        ;
    }
}
