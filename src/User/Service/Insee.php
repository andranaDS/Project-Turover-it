<?php

namespace App\User\Service;

use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class Insee
{
    public const SIRENE_TYPE_SIREN = 'siren';
    public const SIRENE_TYPE_SIRET = 'siret';

    private HttpClientInterface $inseeClient;
    private HttpClientInterface $inseeSireneClient;
    private ?string $accessToken = null;

    public function __construct(HttpClientInterface $inseeClient, HttpClientInterface $inseeSireneClient)
    {
        $this->inseeClient = $inseeClient;
        $this->inseeSireneClient = $inseeSireneClient;
    }

    /**
     * @see https://api.insee.fr/catalogue/site/themes/wso2/subthemes/insee/pages/item-info.jag?name=Sirene&version=V3&provider=insee
     */
    public function searchSirene(string $type, string $value): ?ResponseInterface
    {
        if (false === $this->authenticate()) {
            return null;
        }

        $response = $this->inseeSireneClient->request(
            'GET',
            sprintf('%s/%d', self::SIRENE_TYPE_SIREN === $type ? 'siren' : 'siret', $value),
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                ],
                'timeout' => 2,
            ]
        );

        try {
            if (\in_array($response->getStatusCode(), [Response::HTTP_OK, Response::HTTP_FORBIDDEN], true)) {
                return $response;
            }
        } catch (TransportExceptionInterface $e) {
            // @ignoreException
        }

        return null;
    }

    public function searchSireneSiren(string $value): ?ResponseInterface
    {
        return $this->searchSirene(self::SIRENE_TYPE_SIREN, $value);
    }

    public function isValidSiren(string $value): bool
    {
        // 200 company was found in the api
        // 403 true company was found in the api but not public
        $response = $this->searchSireneSiren($value);

        return null !== $response && \in_array($response->getStatusCode(), [Response::HTTP_OK, Response::HTTP_FORBIDDEN], true);
    }

    public function searchSireneSiret(string $value): ?ResponseInterface
    {
        return $this->searchSirene(self::SIRENE_TYPE_SIRET, $value);
    }

    public function isValidSiret(string $value): bool
    {
        // 200 company was found in the api
        // 403 true company was found in the api but not public
        $response = $this->searchSireneSiret($value);

        return null !== $response && \in_array($response->getStatusCode(), [Response::HTTP_OK, Response::HTTP_FORBIDDEN], true);
    }

    /**
     * @see https://api.insee.fr/catalogue/site/themes/wso2/subthemes/insee/pages/help.jag#renouveler
     */
    public function authenticate(): bool
    {
        if (null !== $this->accessToken) {
            return true;
        }

        $response = $this->inseeClient->request('POST', 'token', [
            'body' => [
                'grant_type' => 'client_credentials',
            ],
            'timeout' => 2,
        ]);

        try {
            if (Response::HTTP_OK === $response->getStatusCode() && null !== ($accessToken = Json::decode($response->getContent(), Json::FORCE_ARRAY)['access_token'] ?? null)) {
                $this->accessToken = $accessToken;

                return true;
            }
        } catch (TransportExceptionInterface|JsonException $e) {
            // @ignoreException
        }

        return false;
    }
}
