<?php

namespace App\Forum\Email;

use App\Core\Enum\EmailSenderType;
use App\Notification\Twig\Email;

class ForumPostReportEmail extends Email
{
    protected ?string $template = 'emails/forum/post_report.email.twig';
    protected ?string $senderType = EmailSenderType::FREE_WORK_CONTACT;
}
