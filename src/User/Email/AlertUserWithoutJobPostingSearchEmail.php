<?php

namespace App\User\Email;

use App\Core\Enum\EmailSenderType;
use App\Notification\Twig\Email;

class AlertUserWithoutJobPostingSearchEmail extends Email
{
    protected ?string $template = 'emails/user/alert_user_without_job_posting_search.email.twig';
    protected ?string $senderType = EmailSenderType::FREE_WORK_JOB;
}
