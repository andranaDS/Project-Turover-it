<?php

namespace App\User\Email;

use App\Core\Enum\EmailSenderType;
use App\Notification\Twig\Email;

class AlertUserMissionsEmail extends Email
{
    protected ?string $template = 'emails/user/alert_mission.email.twig';
    protected ?string $senderType = EmailSenderType::FREE_WORK_JOB;
}
