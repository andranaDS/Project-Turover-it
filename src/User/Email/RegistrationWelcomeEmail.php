<?php

namespace App\User\Email;

use App\Core\Enum\EmailSenderType;
use App\Notification\Twig\Email;

class RegistrationWelcomeEmail extends Email
{
    protected ?string $template = 'emails/user/registration_welcome.email.twig';
    protected ?string $senderType = EmailSenderType::FREE_WORK_ACCOUNT;
}
