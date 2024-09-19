<?php

namespace App\Notification\Enum;

use Greg0ire\Enum\AbstractEnum;

final class NotificationEvent extends AbstractEnum
{
    public const APPLICATION_NEW = 'application_new';
    public const APPLICATION_ABANDONED = 'application_abandoned';
    public const JOB_POSTING_EXPIRING_SOON = 'job_posting_draft_expiring_soon';
    public const SUBSCRIPTION_ENDING_SOON = 'subscription_ending_soon';
    // TODO
}
