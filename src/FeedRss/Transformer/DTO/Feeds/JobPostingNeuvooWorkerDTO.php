<?php

namespace App\FeedRss\Transformer\DTO\Feeds;

use App\FeedRss\Entity\FeedRss;
use App\JobPosting\Entity\JobPosting;
use Symfony\Component\Routing\RouterInterface;

class JobPostingNeuvooWorkerDTO extends AbstractNeuvooDTO
{
    public function __construct(JobPosting $jobPosting, FeedRss $feedRss, RouterInterface $router)
    {
        parent::__construct($jobPosting, $feedRss, $router);

        $this
            ->setSalaryMax($jobPosting->getMaxAnnualSalary())
            ->setSalaryMin($jobPosting->getMinAnnualSalary())
            ->setSalaryPeriod(AbstractNeuvooDTO::SALARY_PERIOD_YEAR)
        ;
    }
}
