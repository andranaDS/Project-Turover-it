<?php

namespace App\Company\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation()
 */
class CompanyIntracommunityVat extends Constraint
{
    public string $messageInvalid = 'core.company_intracommunity_vat.invalid';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
