<?php

namespace App\Core\Util;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class HttpClient
{
    public static function getStatusCode(ResponseInterface $response): ?int
    {
        try {
            $statusCode = $response->getStatusCode();
        } catch (TransportExceptionInterface $e) { // When a network error occurs
            $statusCode = null;
        }

        return $statusCode;
    }
}
