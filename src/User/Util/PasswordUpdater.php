<?php

namespace App\User\Util;

use App\User\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordUpdater implements PasswordUpdaterInterface
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function hashPassword(User $user): void
    {
        $plainPassword = $user->getPlainPassword();

        if (null === $plainPassword || '' === $plainPassword) {
            return;
        }

        $hashedPassword = $this->hasher->hashPassword($user, $plainPassword);

        $user->setPassword($hashedPassword)
            ->eraseCredentials()
        ;
    }
}
