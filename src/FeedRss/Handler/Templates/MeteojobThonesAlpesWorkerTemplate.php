<?php

namespace App\FeedRss\Handler\Templates;

use App\FeedRss\Entity\FeedRss;
use App\FeedRss\Enum\FeedRssPartner;
use App\FeedRss\Enum\FeedRssType;
use App\FeedRss\Transformer\DTO\Feeds\JobPostingMeteojobRhonesAlpesDTO;
use App\JobPosting\Entity\JobPosting;

class MeteojobThonesAlpesWorkerTemplate extends AbstractFeedRssTemplate
{
    public function getType(): string
    {
        return FeedRssType::WORKER;
    }

    public function getPartner(): string
    {
        return FeedRssPartner::METEOJOBRHONESALPES;
    }

    public function getDTOClass(): string
    {
        return JobPostingMeteojobRhonesAlpesDTO::class;
    }

    public function getJobPostings(FeedRss $feedRss): array
    {
        return $this->entityManager->getRepository(JobPosting::class)->findForFeedByRegionSlug($feedRss, 'auvergne-rhone-alpes');
    }

    public function getXmlConfig(): array
    {
        return [
            'template' => 'dynamic',
            'rootName' => 'xml',
            'rootNameParams' => 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"',
            'xmlParams' => 'standalone="yes"',
        ];
    }
}
