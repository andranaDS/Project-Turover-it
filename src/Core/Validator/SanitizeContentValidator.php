<?php

namespace App\Core\Validator;

use HtmlSanitizer\SanitizerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class SanitizeContentValidator extends ConstraintValidator
{
    private SanitizerInterface $sanitizer;

    public function __construct(SanitizerInterface $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof SanitizeContent) {
            throw new UnexpectedTypeException($constraint, SanitizeContent::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if ('' === $this->sanitizer->sanitize($value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}
