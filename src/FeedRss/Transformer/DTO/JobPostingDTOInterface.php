<?php

namespace App\FeedRss\Transformer\DTO;

interface JobPostingDTOInterface
{
    public function getNotRequiredFields(): array;

    public function getParamNameElementFlux(): string;
}
