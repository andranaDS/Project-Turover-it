<?php

namespace App\User\Email;

use App\Core\Enum\EmailSenderType;
use App\Notification\Twig\Email;

class RegistrationEmailConfirmationEmail extends Email
{
    protected ?string $template = 'emails/user/registration_email_confirm.email.twig';
    protected ?string $senderType = EmailSenderType::FREE_WORK_ACCOUNT;
}
