<?php

namespace App\User\Util;

use App\User\Entity\User;

interface PasswordUpdaterInterface
{
    public function hashPassword(User $user): void;
}
