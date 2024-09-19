<?php

namespace App\Recruiter\Controller\Turnover\Recruiter;

use App\Recruiter\Entity\Recruiter;
use Carbon\Carbon;

final class Webinar
{
    public function __invoke(Recruiter $data): Recruiter
    {
        $data->setWebinarViewedAt(Carbon::now());

        return $data;
    }
}
