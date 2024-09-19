<?php

namespace App\Core\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation()
 */
class SanitizeContent extends Constraint
{
    public string $message = 'generic.not_blank';
}
