<?php

namespace App\FeedRss\Handler\Templates;

use App\FeedRss\Enum\FeedRssPartner;
use App\FeedRss\Enum\FeedRssType;
use App\FeedRss\Transformer\DTO\Feeds\JobPostingIndeedContractorDTO;

class IndeedContractorTemplate extends AbstractFeedRssTemplate
{
    public function getType(): string
    {
        return FeedRssType::CONTRACTOR;
    }

    public function getPartner(): string
    {
        return FeedRssPartner::INDEED;
    }

    public function getDTOClass(): string
    {
        return JobPostingIndeedContractorDTO::class;
    }

    public function getXmlConfig(): array
    {
        return [
            'template' => 'indeed',
            'rootName' => 'source',
        ];
    }
}
