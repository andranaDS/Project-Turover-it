<?php

namespace App\User\Email;

use App\Core\Enum\EmailSenderType;
use App\Notification\Twig\Email;

class UserLeadsSummaryEmail extends Email
{
    protected ?string $template = 'emails/user/user_leads_summary.email.twig';
    protected ?string $senderType = EmailSenderType::FREE_WORK_JOB;
}
