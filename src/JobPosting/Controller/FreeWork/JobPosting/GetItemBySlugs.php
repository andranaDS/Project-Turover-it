<?php

namespace App\JobPosting\Controller\FreeWork\JobPosting;

use App\JobPosting\Entity\JobPosting;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetItemBySlugs
{
    public function __invoke(JobPosting $data, string $jobSlug): JobPosting
    {
        if (null === ($job = $data->getJob()) || $job->getSlug() !== $jobSlug) {
            throw new NotFoundHttpException('JobPosting and Job mismatch');
        }

        return $data;
    }
}
