<?php

namespace App\FeedRss\Handler\Templates;

use App\FeedRss\Enum\FeedRssPartner;
use App\FeedRss\Enum\FeedRssType;
use App\FeedRss\Transformer\DTO\Feeds\JobPostingJobijobaContractorDTO;

class JobijobaContractorTemplate extends AbstractFeedRssTemplate
{
    public function getType(): string
    {
        return FeedRssType::CONTRACTOR;
    }

    public function getPartner(): string
    {
        return FeedRssPartner::JOBIJOBA;
    }

    public function getDTOClass(): string
    {
        return JobPostingJobijobaContractorDTO::class;
    }

    public function getXmlConfig(): array
    {
        return [
            'template' => 'jobijoba',
            'rootName' => 'items',
        ];
    }
}
