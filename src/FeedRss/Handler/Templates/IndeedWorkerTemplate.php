<?php

namespace App\FeedRss\Handler\Templates;

use App\FeedRss\Enum\FeedRssPartner;
use App\FeedRss\Enum\FeedRssType;
use App\FeedRss\Transformer\DTO\Feeds\JobPostingIndeedWorkerDTO;

class IndeedWorkerTemplate extends AbstractFeedRssTemplate
{
    public function getType(): string
    {
        return FeedRssType::WORKER;
    }

    public function getPartner(): string
    {
        return FeedRssPartner::INDEED;
    }

    public function getDTOClass(): string
    {
        return JobPostingIndeedWorkerDTO::class;
    }

    public function getXmlConfig(): array
    {
        return [
            'template' => 'indeed',
            'rootName' => 'source',
        ];
    }
}
