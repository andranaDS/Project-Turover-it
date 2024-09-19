<?php

namespace App\User\Security\Guard;

use App\User\Entity\User;

interface ProviderAuthenticatorInterface
{
    public function getProvider(): string;

    public function hydrateUser(User $user, array $data): void;
}
