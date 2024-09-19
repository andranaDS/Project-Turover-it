<?php

namespace App\User\Email;

use App\Core\Enum\EmailSenderType;
use App\Notification\Twig\Email;

class ForgottenPasswordResetEmail extends Email
{
    protected ?string $template = 'emails/user/forgotten_password_reset.email.twig';
    protected ?string $senderType = EmailSenderType::FREE_WORK_ACCOUNT;
}
