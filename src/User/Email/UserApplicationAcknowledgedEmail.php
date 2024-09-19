<?php

namespace App\User\Email;

use App\Core\Enum\EmailSenderType;
use App\Notification\Twig\Email;

class UserApplicationAcknowledgedEmail extends Email
{
    protected ?string $template = 'emails/user/application_acknowledged.email.twig';
    protected ?string $senderType = EmailSenderType::FREE_WORK_JOB;
}
