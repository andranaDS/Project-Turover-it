<?php

namespace App\FeedRss\Handler\Templates;

use App\FeedRss\Enum\FeedRssPartner;
use App\FeedRss\Enum\FeedRssType;
use App\FeedRss\Transformer\DTO\Feeds\JobPostingNeuvooContractorDTO;
use Carbon\Carbon;

class NeuvooContractorTemplate extends AbstractFeedRssTemplate
{
    public function getType(): string
    {
        return FeedRssType::CONTRACTOR;
    }

    public function getPartner(): string
    {
        return FeedRssPartner::NEUVOO;
    }

    public function getDTOClass(): string
    {
        return JobPostingNeuvooContractorDTO::class;
    }

    public function getXmlConfig(): array
    {
        return [
            'template' => 'neuvoo',
            'rootName' => 'source',
            'fieldsBeforeList' => [
                'publisher' => $this->feedRSSPublisher,
                'publisherurl' => $this->candidatesScheme . '://' . $this->candidatesBaseUrl,
                'lastbuilddate' => Carbon::now()->toIso8601String(),
            ],
        ];
    }
}
