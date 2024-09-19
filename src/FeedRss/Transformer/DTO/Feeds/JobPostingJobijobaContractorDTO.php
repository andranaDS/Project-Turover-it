<?php

namespace App\FeedRss\Transformer\DTO\Feeds;

use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Transformer\DTO\JobPostingDTOInterface;
use App\FeedRss\Transformer\RssTransformer;
use App\JobPosting\Entity\JobPosting;
use Symfony\Component\Routing\RouterInterface;

class JobPostingJobijobaContractorDTO extends AbstractJobijobaDTO implements JobPostingDTOInterface
{
    public function __construct(JobPosting $jobPosting, FeedRss $feedRss, RouterInterface $router)
    {
        parent::__construct($jobPosting, $feedRss, $router);

        $this
            ->setSalaryPeriodicity(self::SALARY_PERIODICITY_WEEK)
            ->setSalary(
                $jobPosting->getMinDailySalary() ? $jobPosting->getMinDailySalary() * 5 : null,
                $jobPosting->getMaxDailySalary() ? $jobPosting->getMaxDailySalary() * 5 : null,
            )
        ;
    }

    public function setTitle(?string $title): self
    {
        $this->title = RssTransformer::transformForRss(RssTransformer::transformTitle($title, false) . ' / Freelance');

        return $this;
    }
}
