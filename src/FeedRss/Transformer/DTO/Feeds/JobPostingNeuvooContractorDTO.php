<?php

namespace App\FeedRss\Transformer\DTO\Feeds;

use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Transformer\RssTransformer;
use App\JobPosting\Entity\JobPosting;
use Symfony\Component\Routing\RouterInterface;

class JobPostingNeuvooContractorDTO extends AbstractNeuvooDTO
{
    public function __construct(JobPosting $jobPosting, FeedRss $feedRss, RouterInterface $router)
    {
        parent::__construct($jobPosting, $feedRss, $router);

        $this
            ->setSalaryMax($jobPosting->getMaxDailySalary())
            ->setSalaryMin($jobPosting->getMinDailySalary())
            ->setSalaryPeriod(AbstractNeuvooDTO::SALARY_PERIOD_DAY)
        ;
    }

    public function setTitle(?string $title): self
    {
        $this->title = RssTransformer::transformForRss(RssTransformer::transformTitle($title, false) . ' / Freelance');

        return $this;
    }
}
