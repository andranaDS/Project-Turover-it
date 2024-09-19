<?php

namespace App\User\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SocieteCom
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function isValidSiret(string $value): bool
    {
        $response = $this->client->request(
            'GET',
            "https://www.societe.com/etablissement/-$value.html"
        );

        try {
            if (Response::HTTP_OK === $response->getStatusCode()) {
                return true;
            }
        } catch (TransportExceptionInterface $e) {
            // @ignoreException
        }

        return false;
    }

    public function isValidSiren(string $value): bool
    {
        $response = $this->client->request(
            'GET',
            "https://www.societe.com/societe/-$value.html"
        );

        try {
            if (Response::HTTP_OK === $response->getStatusCode()) {
                return true;
            }
        } catch (TransportExceptionInterface $e) {
            // @ignoreException
        }

        return false;
    }
}
