<?php

namespace App\User\Email;

use App\Core\Enum\EmailSenderType;
use App\Notification\Twig\Email;

class ImmediateAvailabilityConfirmEmail extends Email
{
    protected ?string $template = 'emails/user/immediate_availability_confirm.email.twig';
    protected ?string $senderType = EmailSenderType::FREE_WORK_PROFILE;
}
