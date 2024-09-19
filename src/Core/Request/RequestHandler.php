<?php

namespace App\Core\Request;

use Symfony\Component\HttpFoundation\Request;

class RequestHandler
{
    private string $turnoverBaseUrl;
    private string $freeWorkBaseUrl;

    public function __construct(string $turnoverBaseUrl, string $freeWorkBaseUrl)
    {
        $this->turnoverBaseUrl = $turnoverBaseUrl;
        $this->freeWorkBaseUrl = $freeWorkBaseUrl;
    }

    public function isTurnover(Request $request): bool
    {
        return $request->getHost() === $this->turnoverBaseUrl;
    }

    public function isFreeWork(Request $request): bool
    {
        return $request->getHost() === $this->freeWorkBaseUrl;
    }
}
