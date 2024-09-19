<?php

namespace App\Core\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation()
 */
class CompanyRegistrationNumber extends Constraint
{
    public string $countryCodeProperty;
    public string $registrationNumberProperty;
    public array $parameters = [];
    public string $messageInvalid = 'core.company_registration_number.invalid';
    public string $messageLength = 'core.company_registration_number.length';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
