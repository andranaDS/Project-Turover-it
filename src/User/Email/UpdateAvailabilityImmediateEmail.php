<?php

namespace App\User\Email;

use App\Core\Enum\EmailSenderType;
use App\Notification\Twig\Email;

class UpdateAvailabilityImmediateEmail extends Email
{
    protected ?string $template = 'emails/user/update_availability_immediate.email.twig';
    protected ?string $senderType = EmailSenderType::FREE_WORK_PROFILE;
}
