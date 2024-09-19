<?php

namespace App\Recruiter\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation()
 */
class Teammate extends Constraint
{
    public string $message = 'The account does not belong to your company';
}
