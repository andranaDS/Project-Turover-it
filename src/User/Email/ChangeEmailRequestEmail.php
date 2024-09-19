<?php

namespace App\User\Email;

use App\Notification\Twig\Email;

class ChangeEmailRequestEmail extends Email
{
    protected ?string $template = 'emails/user/change_email_request.email.twig';
}
