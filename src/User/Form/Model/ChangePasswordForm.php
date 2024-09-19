<?php

namespace App\User\Form\Model;

use App\Core\Validator as CoreAssert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class ChangePasswordForm
{
    /**
     * @SecurityAssert\UserPassword(message="user.password.old_password")
     */
    private ?string $oldPassword = null;

    /**
     * @CoreAssert\PasswordComplexity(minScore=2)
     */
    private ?string $newPassword = null;

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(?string $oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(?string $newPassword): void
    {
        $this->newPassword = $newPassword;
    }
}
