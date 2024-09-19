<?php

namespace App\User\Email;

use App\Core\Enum\EmailSenderType;
use App\Notification\Twig\Email;

class UserNotificationForumTopicReplyEmail extends Email
{
    protected ?string $template = 'emails/user/user_notification_forum_topic_reply.email.twig';
    protected ?string $notification = 'forumTopicReply';
    protected ?string $senderType = EmailSenderType::FREE_WORK_FORUM;
}
