<?php

namespace App\User\Validator;

use App\User\Entity\User;

class UserJobSearchPreferencesValidationGroups
{
    /**
     * @return string[]
     */
    public static function validationGroups(User $user): array
    {
        $groups = ['user:patch:job_search_preferences'];

        if (true === $user->getFreelance()) {
            $groups[] = 'user:patch:job_search_preferences:free';
        }

        if (true === $user->getEmployee()) {
            $groups[] = 'user:patch:job_search_preferences:worker';
        }

        if (true === $user->getInsurance()) {
            $groups[] = 'user:patch:job_search_preferences:insurance';
        }

        return $groups;
    }
}
