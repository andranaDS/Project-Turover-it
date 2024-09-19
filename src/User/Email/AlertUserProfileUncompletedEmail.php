<?php

namespace App\User\Email;

use App\Core\Enum\EmailSenderType;
use App\Notification\Twig\Email;

class AlertUserProfileUncompletedEmail extends Email
{
    protected ?string $template = 'emails/user/alert_user_profile_uncompleted.email.twig';
    protected ?string $senderType = EmailSenderType::FREE_WORK_PROFILE;
}
