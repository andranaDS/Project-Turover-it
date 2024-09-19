<?php

namespace App\Recruiter\Model;

use App\Core\Validator as CoreAssert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class ChangePasswordForm
{
    /**
     * @SecurityAssert\UserPassword(message="recruiter.password.old_password", groups={"change_password:old_password"})
     */
    private ?string $oldPassword = null;

    /**
     * @CoreAssert\PasswordComplexity(minScore=2, groups={"change_password:new_password"})
     */
    private ?string $newPassword = null;

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(?string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(?string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }
}
