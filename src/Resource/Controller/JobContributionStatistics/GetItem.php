<?php

namespace App\Resource\Controller\JobContributionStatistics;

use App\Core\Entity\Job;
use App\Resource\Entity\JobContributionStatistics;
use App\Resource\Repository\JobContributionStatisticsRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GetItem
{
    public function __invoke(Job $job, JobContributionStatisticsRepository $jobContributionStatisticsRepository): JobContributionStatistics
    {
        $jobContributionStatistics = $jobContributionStatisticsRepository->findOneBy(
            ['job' => $job],
            ['day' => Criteria::DESC]
        );

        if (null === $jobContributionStatistics) {
            throw new HttpException(Response::HTTP_NO_CONTENT);
        }

        return $jobContributionStatistics;
    }
}
