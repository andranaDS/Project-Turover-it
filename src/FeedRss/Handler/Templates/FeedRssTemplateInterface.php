<?php

namespace App\FeedRss\Handler\Templates;

interface FeedRssTemplateInterface
{
    public function getType(): string;

    public function getPartner(): string;

    public function getDTOClass(): string;
}
