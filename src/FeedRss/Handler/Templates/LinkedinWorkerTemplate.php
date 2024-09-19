<?php

namespace App\FeedRss\Handler\Templates;

use App\FeedRss\Enum\FeedRssPartner;
use App\FeedRss\Enum\FeedRssType;
use App\FeedRss\Transformer\DTO\Feeds\JobPostingLinkedinWorkerDTO;

class LinkedinWorkerTemplate extends AbstractFeedRssTemplate
{
    public function getType(): string
    {
        return FeedRssType::WORKER;
    }

    public function getPartner(): string
    {
        return FeedRssPartner::LINKEDIN;
    }

    public function getDTOClass(): string
    {
        return JobPostingLinkedinWorkerDTO::class;
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
