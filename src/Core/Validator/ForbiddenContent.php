<?php

namespace App\Core\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation()
 */
class ForbiddenContent extends Constraint
{
    public string $message = 'core.forbidden_content';
}
