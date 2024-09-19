<?php

namespace App\Forum\Email;

use App\Core\Enum\EmailSenderType;
use App\Notification\Twig\Email;

class ForumStatisticsEmail extends Email
{
    protected ?string $template = 'emails/forum/statistics.email.twig';
    protected ?string $senderType = EmailSenderType::FREE_WORK_FORUM;
}
