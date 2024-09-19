<?php

namespace App\FeedRss\Transformer\DTO\Feeds;

use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Transformer\DTO\JobPostingDTOInterface;
use App\FeedRss\Transformer\RssTransformer;
use App\JobPosting\Entity\JobPosting;
use Symfony\Component\Routing\RouterInterface;

class JobPostingJobrapidoContractorDTO extends AbstractJobrapidoDTO implements JobPostingDTOInterface
{
    public function __construct(JobPosting $jobPosting, FeedRss $feedRss, RouterInterface $router)
    {
        parent::__construct($jobPosting, $feedRss, $router);

        $this->setSalary($jobPosting->getMinDailySalary(), $jobPosting->getMaxDailySalary(), $jobPosting->getCurrency(), self::SALARY_PERIOD_CONTRACTOR);
    }

    public function setTitle(?string $title): self
    {
        $this->title = RssTransformer::transformForRss(RssTransformer::transformTitle($title, false) . ' / Freelance');

        return $this;
    }
}
