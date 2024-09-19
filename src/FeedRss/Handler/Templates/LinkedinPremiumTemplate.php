<?php

namespace App\FeedRss\Handler\Templates;

use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Enum\FeedRssPartner;
use App\FeedRss\Enum\FeedRssType;
use App\FeedRss\Transformer\DTO\Feeds\JobPostingLinkedinPremiumDTO;
use App\JobPosting\Entity\JobPosting;

class LinkedinPremiumTemplate extends AbstractFeedRssTemplate
{
    private const JOB_POSTING_LIMIT = 200;

    public function getType(): string
    {
        return FeedRssType::PREMIUM;
    }

    public function getPartner(): string
    {
        return FeedRssPartner::LINKEDIN;
    }

    public function getDTOClass(): string
    {
        return JobPostingLinkedinPremiumDTO::class;
    }

    public function getXmlConfig(): array
    {
        return [
            'template' => 'linkedin',
            'rootName' => 'source',
            'fieldsBeforeList' => [
                'lastBuildDate' => date('r'),
                'publisherUrl' => $this->candidatesScheme . '://' . $this->candidatesBaseUrl,
                'publisher' => $this->feedRSSPublisher,
            ],
        ];
    }

    public function getJobPostings(FeedRss $feedRss): array
    {
        return $this->entityManager->getRepository(JobPosting::class)->findMostViewed($feedRss, self::JOB_POSTING_LIMIT);
    }
}
