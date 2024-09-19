<?php

namespace App\Core\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation()
 */
class Location extends Constraint
{
    public string $message = 'Please fill in the country.';
}
