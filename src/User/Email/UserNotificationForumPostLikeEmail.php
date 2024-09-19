<?php

namespace App\User\Email;

use App\Core\Enum\EmailSenderType;
use App\Notification\Twig\Email;

class UserNotificationForumPostLikeEmail extends Email
{
    protected ?string $template = 'emails/user/user_notification_forum_post_like.email.twig';
    protected ?string $notification = 'forumPostLike';
    protected ?string $senderType = EmailSenderType::FREE_WORK_FORUM;
}
