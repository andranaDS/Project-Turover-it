<?php

namespace App\Core\Email;

use App\Notification\Twig\Email;

class ContactEmail extends Email
{
    protected ?string $template = 'emails/core/contact.email.twig';
}
