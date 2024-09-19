<?php

namespace App\User\Email;

use App\Core\Enum\EmailSenderType;
use App\Notification\Twig\Email;

class UserNotificationForumPostReplyEmail extends Email
{
    protected ?string $template = 'emails/user/user_notification_forum_post_reply.email.twig';
    protected ?string $notification = 'forumPostReply';
    protected ?string $senderType = EmailSenderType::FREE_WORK_FORUM;
}
