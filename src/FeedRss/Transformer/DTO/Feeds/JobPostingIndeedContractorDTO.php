<?php

namespace App\FeedRss\Transformer\DTO\Feeds;

use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Transformer\DTO\JobPostingDTOInterface;
use App\FeedRss\Transformer\RssTransformer;
use App\JobPosting\Entity\JobPosting;
use Symfony\Component\Routing\RouterInterface;

class JobPostingIndeedContractorDTO extends AbstractIndeedDTO implements JobPostingDTOInterface
{
    public function __construct(JobPosting $jobPosting, FeedRss $feedRss, RouterInterface $router)
    {
        parent::__construct($jobPosting, $feedRss, $router);

        $this->setSalary($jobPosting->getMinDailySalary(), $jobPosting->getMaxDailySalary(), $jobPosting->getCurrency());
    }

    public function setSalary(?int $minSalary, ?int $maxSalary, ?string $currency): void
    {
        $currency = RssTransformer::transformCurrency($currency);
        $salary = '';

        if (null !== $minSalary && null !== $maxSalary) {
            $salary = sprintf(
                '[%s%d-%s%d per day]',
                $currency,
                $minSalary,
                $currency,
                $maxSalary
            );
        } elseif (null !== $minSalary || null !== $maxSalary) {
            $salary = sprintf(
                '[%s%d per day]',
                $currency,
                $minSalary ?? $maxSalary,
            );
        }

        $this->salary = RssTransformer::transformForRss($salary);
    }

    public function setTitle(?string $title): self
    {
        $this->title = RssTransformer::transformForRss(RssTransformer::transformTitle($title, false) . ' / Freelance');

        return $this;
    }
}
