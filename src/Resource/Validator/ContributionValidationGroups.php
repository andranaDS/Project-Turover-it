<?php

namespace App\Resource\Validator;

use App\JobPosting\Enum\Contract;
use App\Resource\Entity\Contribution;

class ContributionValidationGroups
{
    /**
     * @return string[]
     */
    public static function validationGroups(Contribution $contribution): array
    {
        $groups = ['Default'];

        if (Contract::isFree($contribution->getContract())) {
            $groups[] = 'contribution:post:free';
        }

        if (Contract::isWork($contribution->getContract())) {
            $groups[] = 'contribution:post:work';
        }

        if (Contract::PERMANENT !== $contribution->getContract()) {
            $groups[] = 'contribution:post:none-permanent';
        }

        return $groups;
    }
}
