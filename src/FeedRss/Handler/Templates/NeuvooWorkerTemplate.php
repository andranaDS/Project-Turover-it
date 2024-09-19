<?php

namespace App\FeedRss\Handler\Templates;

use App\FeedRss\Enum\FeedRssPartner;
use App\FeedRss\Enum\FeedRssType;
use App\FeedRss\Transformer\DTO\Feeds\JobPostingNeuvooWorkerDTO;
use Carbon\Carbon;

class NeuvooWorkerTemplate extends AbstractFeedRssTemplate
{
    public function getType(): string
    {
        return FeedRssType::WORKER;
    }

    public function getPartner(): string
    {
        return FeedRssPartner::NEUVOO;
    }

    public function getDTOClass(): string
    {
        return JobPostingNeuvooWorkerDTO::class;
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