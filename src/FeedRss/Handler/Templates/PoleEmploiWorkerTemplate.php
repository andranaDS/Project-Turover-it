<?php

namespace App\FeedRss\Handler\Templates;

use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Enum\FeedRssPartner;
use App\FeedRss\Enum\FeedRssType;
use App\FeedRss\Transformer\DTO\Feeds\JobPostingPoleEmploiWorkerDTO;
use App\JobPosting\Entity\JobPosting;

class PoleEmploiWorkerTemplate extends AbstractFeedRssTemplate
{
    public function getType(): string
    {
        return FeedRssType::WORKER;
    }

    public function getPartner(): string
    {
        return FeedRssPartner::POLEEMPLOI;
    }

    public function getDTOClass(): string
    {
        return JobPostingPoleEmploiWorkerDTO::class;
    }

    public function getJobPostings(FeedRss $feedRss): array
    {
        return $this->entityManager->getRepository(JobPosting::class)->findForFeedByCountryCode($feedRss, 'FR');
    }

    public function getXmlConfig(): array
    {
        return [
            'template' => 'pole-emploi',
            'rootName' => 'Offre',
        ];
    }
}
