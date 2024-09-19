<?php

namespace App\User\Email;

use App\Core\Enum\EmailSenderType;
use App\Notification\Twig\Email;

class NoImmediateAvailabilityConfirmEmail extends Email
{
    protected ?string $template = 'emails/user/no_immediate_availability_confirm.email.twig';
    protected ?string $senderType = EmailSenderType::FREE_WORK_PROFILE;
}
