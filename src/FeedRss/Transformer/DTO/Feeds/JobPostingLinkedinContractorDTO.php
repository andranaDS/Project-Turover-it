<?php

namespace App\FeedRss\Transformer\DTO\Feeds;

use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Transformer\DTO\JobPostingDTOInterface;
use App\FeedRss\Transformer\RssTransformer;
use App\JobPosting\Entity\JobPosting;
use Symfony\Component\Routing\RouterInterface;

class JobPostingLinkedinContractorDTO extends AbstractLinkedinDTO implements JobPostingDTOInterface
{
    public function __construct(JobPosting $jobPosting, FeedRss $feedRss, RouterInterface $router)
    {
        parent::__construct($jobPosting, $feedRss, $router);

        $this
            ->setSalaryHighEndAmount($jobPosting->getMaxDailySalary())
            ->setSalaryLowEndAmount($jobPosting->getMinDailySalary())
            ->setSalaryPeriod(AbstractLinkedinDTO::SALARY_PERIOD_DAILY)
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

    public function setTitle(?string $title): self
    {
        $title = RssTransformer::transformTitle($title, false);

        $this->title = $title . ' / Freelance';

        return $this;
    }
}
