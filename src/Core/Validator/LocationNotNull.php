<?php

namespace App\Core\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation()
 */
class LocationNotNull extends Constraint
{
    public string $message = 'generic.not_blank';
}
