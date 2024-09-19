<?php

namespace App\Recruiter\Email;

use App\Notification\Mailjet\Email;

class ForgottenPasswordRequestEmail extends Email
{
    protected ?int $templateId = 4109270;
}
