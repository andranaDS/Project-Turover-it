<?php

namespace App\FeedRss\Handler\Templates;

use App\FeedRss\Enum\FeedRssPartner;
use App\FeedRss\Enum\FeedRssType;
use App\FeedRss\Transformer\DTO\Feeds\JobPostingJoobleWorkerDTO;

class JoobleWorkerTemplate extends AbstractFeedRssTemplate
{
    public function getType(): string
    {
        return FeedRssType::WORKER;
    }

    public function getPartner(): string
    {
        return FeedRssPartner::JOOBLE;
    }

    public function getDTOClass(): string
    {
        return JobPostingJoobleWorkerDTO::class;
    }

    public function getXmlConfig(): array
    {
        return [
            'template' => 'jooble',
            'rootName' => 'jobs',
            'fieldsBeforeList' => [
                'publisher' => $this->feedRSSPublisher,
                'publisherurl' => $this->candidatesScheme . '://' . $this->candidatesBaseUrl,
            ],
        ];
    }
}
