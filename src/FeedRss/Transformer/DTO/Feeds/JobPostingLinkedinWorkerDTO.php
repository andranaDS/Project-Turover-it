<?php

namespace App\FeedRss\Transformer\DTO\Feeds;

use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Transformer\DTO\JobPostingDTOInterface;
use App\JobPosting\Entity\JobPosting;
use Symfony\Component\Routing\RouterInterface;

class JobPostingLinkedinWorkerDTO extends AbstractLinkedinDTO implements JobPostingDTOInterface
{
    public function __construct(JobPosting $jobPosting, FeedRss $feedRss, RouterInterface $router)
    {
        parent::__construct($jobPosting, $feedRss, $router);

        $this
            ->setSalaryHighEndAmount($jobPosting->getMaxAnnualSalary())
            ->setSalaryLowEndAmount($jobPosting->getMinAnnualSalary())
            ->setSalaryPeriod(AbstractLinkedinDTO::SALARY_PERIOD_YEARLY)
        ;
    }

    public function getNotRequiredFields(): array
    {
        return [];
    }

    public function getParamNameElementFlux(): string
    {
        return 'job';
    }
}
