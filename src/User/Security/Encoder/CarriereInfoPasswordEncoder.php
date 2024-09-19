<?php

namespace App\User\Security\Encoder;

use Symfony\Component\PasswordHasher\LegacyPasswordHasherInterface;

class CarriereInfoPasswordEncoder implements LegacyPasswordHasherInterface
{
    public function hash(string $plainPassword, string $salt = null): string
    {
        if (null === $salt) {
            $salt = substr(md5(uniqid((string) mt_rand(), true)), 0, 21);
        } else {
            $salt = substr($salt, 0, 21);
        }

        return $salt . hash('sha512', $salt . $plainPassword);
    }

    public function verify(string $hashedPassword, string $plainPassword, string $salt = null): bool
    {
        return $hashedPassword === $this->hash($plainPassword, $hashedPassword);
    }

    public function needsRehash(string $encoded): bool
    {
        return false;
    }
}
