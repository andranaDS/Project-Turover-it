<?php

namespace App\User\Security\Guard;

use App\User\Entity\User;
use App\User\Enum\Provider;

class LinkedInAuthenticator extends AbstractProviderAuthenticator
{
    public function getProvider(): string
    {
        return Provider::LINKEDIN;
    }

    public function hydrateUser(User $user, array $data): void
    {
        $user->setFirstName($data['localizedFirstName'])
            ->setLastName($data['localizedLastName'])
        ;
    }
}
