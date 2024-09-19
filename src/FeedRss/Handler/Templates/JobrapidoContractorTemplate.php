<?php

namespace App\FeedRss\Handler\Templates;

use App\FeedRss\Enum\FeedRssPartner;
use App\FeedRss\Enum\FeedRssType;
use App\FeedRss\Transformer\DTO\Feeds\JobPostingJobrapidoContractorDTO;

class JobrapidoContractorTemplate extends AbstractFeedRssTemplate
{
    public function getType(): string
    {
        return FeedRssType::CONTRACTOR;
    }

    public function getPartner(): string
    {
        return FeedRssPartner::JOBRAPIDO;
    }

    public function getDTOClass(): string
    {
        return JobPostingJobrapidoContractorDTO::class;
    }

    public function getXmlConfig(): array
    {
        return [
            'template' => 'jobrapido',
            'rootName' => 'jobs',
        ];
    }
}
