<?php

namespace App\User\Email;

use App\Core\Enum\EmailSenderType;
use App\Notification\Twig\Email;

class UserNotificationForumTopicFavoriteEmail extends Email
{
    protected ?string $template = 'emails/user/user_notification_forum_topic_favorite.email.twig';
    protected ?string $notification = 'forumTopicFavorite';
    protected ?string $senderType = EmailSenderType::FREE_WORK_FORUM;
}
