<?php

namespace App\Core\Email;

use App\Notification\Twig\Email;

class SensitiveContentEmail extends Email
{
    protected ?string $template = 'emails/core/sensitive_content.email.twig';
}
