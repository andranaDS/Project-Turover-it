<?php

namespace App\User\Security\Guard;

use App\User\Entity\User;
use App\User\Enum\Provider;

class GoogleAuthenticator extends AbstractProviderAuthenticator
{
    public function getProvider(): string
    {
        return Provider::GOOGLE;
    }

    public function hydrateUser(User $user, array $data): void
    {
        $user->setFirstName($data['given_name'] ?? null)
            ->setLastName($data['family_name'] ?? null)
        ;
    }
}
