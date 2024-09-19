<?php

namespace App\FeedRss\Transformer\DTO\Feeds;

use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Transformer\DTO\JobPostingDTOInterface;
use App\JobPosting\Entity\JobPosting;
use Symfony\Component\Routing\RouterInterface;

class JobPostingJobrapidoWorkerDTO extends AbstractJobrapidoDTO implements JobPostingDTOInterface
{
    public function __construct(JobPosting $jobPosting, FeedRss $feedRss, RouterInterface $router)
    {
        parent::__construct($jobPosting, $feedRss, $router);

        $this->setSalary($jobPosting->getMinAnnualSalary(), $jobPosting->getMaxAnnualSalary(), $jobPosting->getCurrency(), self::SALARY_PERIOD_WORKER);
    }
}
