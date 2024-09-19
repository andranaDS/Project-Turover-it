<?php

namespace App\User\Email;

use App\Core\Enum\EmailSenderType;
use App\Notification\Twig\Email;

class UpdateConfirmationAvailabilityNonImmediateEmail extends Email
{
    protected ?string $template = 'emails/user/update_confirmation_availability_non_immediate.email.twig';
    protected ?string $senderType = EmailSenderType::FREE_WORK_PROFILE;
}
