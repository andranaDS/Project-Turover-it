<?php

namespace App\JobPosting\Tests\Functional\FreeWork\JobPosting;

use App\Tests\Functional\ApiTestCase;

class JobPostingsFavoriteGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/job_postings/favorites');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/job_postings/favorites');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('GET', '/job_postings/favorites');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testWithFavorites(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
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
                    'title' => 'Responsable cybersécurité (sans management) (H/F)',
                ],
                [
                    '@type' => 'JobPosting',
                    'title' => 'Responsable applicatifs Finance (H/F) (CDI)',
                ],
            ],
            'hydra:totalItems' => 2,
        ]);
    }

    public function testWithoutFavorite(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('vincent.van-gogh@free-work.fr');
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
