<?php

namespace App\Company\Validator;

use App\Company\Entity\Company;
use App\User\Enum\CompanyCountryCode;

class CompanyAccountValidationGroups
{
    /**
     * @return string[]
     */
    public static function validationGroups(Company $company): array
    {
        $groups = ['company:patch:account'];

        if (null !== $billingAddress = $company->getBillingAddress()) {
            if (CompanyCountryCode::FR === $billingAddress->getCountryCode()) {
                $groups[] = 'user:patch:account:registration_number';
            } else {
                $groups[] = 'user:patch:account:intracommunity_vat';
            }
        }

        return $groups;
    }
}
