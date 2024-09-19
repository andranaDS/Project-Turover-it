<?php

namespace App\User\Email;

use App\Core\Enum\EmailSenderType;
use App\Notification\Twig\Email;

class ImmediateAvailabilitySecondRelaunchEmail extends Email
{
    protected ?string $template = 'emails/user/immediate_availability_confirm_second_relaunch.email.twig';
    protected ?string $senderType = EmailSenderType::FREE_WORK_PROFILE;
}
