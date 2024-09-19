<?php

namespace App\JobPosting\Tests\Functional\FreeWork\JobPosting;

use App\Tests\Functional\ApiTestCase;

class JobPostingsRecruiterFavoriteGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/job_postings/favorites');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/job_postings/favorites');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testWithFavorites(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/job_postings/favorites');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/JobPosting',
            '@id' => '/job_postings',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@type' => 'JobPosting',
                    'title' => 'Responsable applicatifs Finance (H/F) (CDI)',
                ],
            ],
            'hydra:totalItems' => 1,
        ]);
    }

    public function testWithoutFavorite(): void
    {
        $client = static::createTurnoverAuthenticatedClient('gustavo.fring@breaking-bad.com');
        $client->request('GET', '/job_postings/favorites');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/JobPosting',
            '@id' => '/job_postings',
            '@type' => 'hydra:Collection',
            'hydra:member' => [],
            'hydra:totalItems' => 0,
        ]);
    }
}
