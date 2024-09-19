<?php

namespace App\User\Email;

use App\Core\Enum\EmailSenderType;
use App\Notification\Twig\Email;

class ProfileNotVisibleEmail extends Email
{
    protected ?string $template = 'emails/user/profile_not_visible.email.twig';
    protected ?string $senderType = EmailSenderType::FREE_WORK_PROFILE;
}
