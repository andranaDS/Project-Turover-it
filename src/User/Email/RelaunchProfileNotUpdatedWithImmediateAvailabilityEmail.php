<?php

namespace App\User\Email;

use App\Core\Enum\EmailSenderType;
use App\Notification\Twig\Email;

class RelaunchProfileNotUpdatedWithImmediateAvailabilityEmail extends Email
{
    protected ?string $template = 'emails/user/relaunch_profile_not_updated_with_immediate_availability.email.twig';
    protected ?string $senderType = EmailSenderType::FREE_WORK_PROFILE;
}
