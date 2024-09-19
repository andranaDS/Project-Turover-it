<?php

namespace App\FeedRss\Transformer\DTO\Feeds;

use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Transformer\DTO\JobPostingDTOInterface;
use App\JobPosting\Entity\JobPosting;
use Symfony\Component\Routing\RouterInterface;

class JobPostingJobijobaWorkerDTO extends AbstractJobijobaDTO implements JobPostingDTOInterface
{
    public function __construct(JobPosting $jobPosting, FeedRss $feedRss, RouterInterface $router)
    {
        parent::__construct($jobPosting, $feedRss, $router);

        $this
            ->setSalaryPeriodicity(self::SALARY_PERIODICITY_YEAR)
            ->setSalary($jobPosting->getMinAnnualSalary(), $jobPosting->getMaxAnnualSalary())
        ;
    }
}
