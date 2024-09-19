<?php

namespace App\User\Validator;

use App\User\Entity\User;
use App\User\Enum\Availability;

class UserStatusValidationGroups
{
    /**
     * @return string[]
     */
    public static function validationGroups(User $user): array
    {
        $groups = ['user:patch:status'];

        if (Availability::DATE === $user->getAvailability()) {
            $groups[] = 'user:patch:status:date';
        }

        return $groups;
    }
}
