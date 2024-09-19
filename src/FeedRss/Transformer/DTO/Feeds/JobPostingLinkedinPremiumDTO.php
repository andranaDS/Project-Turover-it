<?php

namespace App\FeedRss\Transformer\DTO\Feeds;

use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Transformer\DTO\JobPostingDTOInterface;
use App\FeedRss\Transformer\RssTransformer;
use App\JobPosting\Entity\JobPosting;
use Symfony\Component\Routing\RouterInterface;

class JobPostingLinkedinPremiumDTO extends AbstractLinkedinDTO implements JobPostingDTOInterface
{
    public function __construct(JobPosting $jobPosting, FeedRss $feedRss, RouterInterface $router)
    {
        parent::__construct($jobPosting, $feedRss, $router);

        $isFreeContract = $jobPosting->hasFreeContract() && !$jobPosting->hasWorkContract();

        $this
            ->setSalaryHighEndAmount($isFreeContract ? $jobPosting->getMaxDailySalary() : $jobPosting->getMaxAnnualSalary())
            ->setSalaryLowEndAmount($isFreeContract ? $jobPosting->getMinDailySalary() : $jobPosting->getMinAnnualSalary())
            ->setSalaryPeriod($isFreeContract ? AbstractLinkedinDTO::SALARY_PERIOD_DAILY : AbstractLinkedinDTO::SALARY_PERIOD_YEARLY)
        ;

        if ($isFreeContract) {
            $this->setFreelanceTitle($jobPosting->getTitle());
        }
    }

    public function getNotRequiredFields(): array
    {
        return [];
    }

    public function getParamNameElementFlux(): string
    {
        return 'job';
    }

    public function setFreelanceTitle(?string $title): self
    {
        $this->title = RssTransformer::transformTitle($title, false) . ' / Freelance';

        return $this;
    }
}
