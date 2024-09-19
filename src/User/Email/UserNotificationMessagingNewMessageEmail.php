<?php

namespace App\User\Email;

use App\Core\Enum\EmailSenderType;
use App\Notification\Twig\Email;

class UserNotificationMessagingNewMessageEmail extends Email
{
    protected ?string $template = 'emails/user/user_notification_messaging_new_message.email.twig';
    protected ?string $notification = 'messagingNewMessage';
    protected ?string $senderType = EmailSenderType::FREE_WORK_FORUM;
}
