<?php

namespace App\FeedRss\Handler\Templates;

use App\FeedRss\Enum\FeedRssPartner;
use App\FeedRss\Enum\FeedRssType;
use App\FeedRss\Transformer\DTO\Feeds\JobPostingLinkedinContractorDTO;

class LinkedinContractorTemplate extends AbstractFeedRssTemplate
{
    public function getType(): string
    {
        return FeedRssType::CONTRACTOR;
    }

    public function getPartner(): string
    {
        return FeedRssPartner::LINKEDIN;
    }

    public function getDTOClass(): string
    {
        return JobPostingLinkedinContractorDTO::class;
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
}
