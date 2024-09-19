<?php

namespace App\Core\Enum;

use Greg0ire\Enum\AbstractEnum;

final class EmailSenderType extends AbstractEnum
{
    public const FREE_WORK_CONTACT = 'free_work_contact';
    public const FREE_WORK_PROFILE = 'free_work_profile';
    public const FREE_WORK_JOB = 'free_work_job';
    public const FREE_WORK_ACCOUNT = 'free_work_account';
    public const FREE_WORK_FORUM = 'free_work_forum';
    public const TURNOVER_CONTACT = 'turnover_contact';
}
