<?php

namespace App\JobPosting\Tests\Functional\FreeWork\JobPostingSearch;

use App\JobPosting\Enum\Contract;
use App\JobPosting\Enum\PublishedSince;
use App\Tests\Functional\ApiTestCase;

class JobPostingSearchGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/job_posting_searches/1');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/job_posting_searches/1');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsOwner(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/job_posting_searches/1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('GET', '/job_posting_searches/1');

        self::assertResponseStatusCodeSame(403);
    }

    public function testWithData(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/job_posting_searches/1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/JobPostingSearch',
            '@type' => 'JobPostingSearch',
            'title' => 'JobPostingSearch 1 - User 6',
            'searchKeywords' => 'php,java',
            'locations' => [
                [
                    '@type' => 'JobPostingSearchLocation',
                    'location' => [
                        '@type' => 'Location',
                        'street' => null,
                        'locality' => 'Paris',
                        'postalCode' => null,
                        'adminLevel1' => 'Île-de-France',
                        'adminLevel2' => null,
                        'country' => 'France',
                        'countryCode' => 'FR',
                        'latitude' => '48.8588897',
                        'longitude' => '2.3200410',
                    ],
                ],
            ],
            'contracts' => [Contract::PERMANENT, Contract::FIXED_TERM],
            'minAnnualSalary' => null,
            'minDailySalary' => null,
            'currency' => null,
            'minDuration' => null,
            'maxDuration' => null,
            'remoteMode' => null,
            'publishedSince' => PublishedSince::LESS_THAN_24_HOURS,
        ]);
    }

    public function testWithInvalidJobPostingSearchId(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $client->request('GET', '/job_posting_searches/1337');
        self::assertResponseStatusCodeSame(404);
    }
}
