<?php

namespace App\Partner\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation()
 */
class PartnerDistribution extends Constraint
{
    public string $messageDistributionInvalid = 'partner.distribution_invalid';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
