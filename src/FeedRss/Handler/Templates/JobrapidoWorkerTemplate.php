<?php

namespace App\FeedRss\Handler\Templates;

use App\FeedRss\Enum\FeedRssPartner;
use App\FeedRss\Enum\FeedRssType;
use App\FeedRss\Transformer\DTO\Feeds\JobPostingJobrapidoWorkerDTO;

class JobrapidoWorkerTemplate extends AbstractFeedRssTemplate
{
    public function getType(): string
    {
        return FeedRssType::WORKER;
    }

    public function getPartner(): string
    {
        return FeedRssPartner::JOBRAPIDO;
    }

    public function getDTOClass(): string
    {
        return JobPostingJobrapidoWorkerDTO::class;
    }

    public function getXmlConfig(): array
    {
        return [
            'template' => 'jobrapido',
            'rootName' => 'jobs',
        ];
    }
}
