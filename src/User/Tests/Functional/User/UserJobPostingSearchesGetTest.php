<?php

namespace App\User\Tests\Functional\User;

use App\JobPosting\Enum\Contract;
use App\JobPosting\Enum\PublishedSince;
use App\JobPosting\Enum\RemoteMode;
use App\Tests\Functional\ApiTestCase;

class UserJobPostingSearchesGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/users/1/job_posting_searches');

        self::assertResponseStatusCodeSame(401);
    }

    public function testWithNonExistentUser(): void
    {
        $client = static::createFreeWorkAuthenticatedClient();
        $client->request('GET', '/users/user-non-existent/job_posting_searches');

        self::assertResponseStatusCodeSame(403);
    }

    public function testOnInOtherEntity(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/users/3/job_posting_searches');

        self::assertResponseStatusCodeSame(403);
    }

    public function testWithoutDocuments(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('elisabeth.vigee-le-brun@free-work.fr');
        $client->request('GET', '/users/11/job_posting_searches');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/JobPostingSearch',
            '@id' => '/users/11/job_posting_searches',
            '@type' => 'hydra:Collection',
            'hydra:member' => [],
            'hydra:totalItems' => 0,
        ]);
    }

    public function testWithDocuments(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/users/6/job_posting_searches');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/JobPostingSearch',
            '@id' => '/users/6/job_posting_searches',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@type' => 'JobPostingSearch',
                    'searchKeywords' => null,
                    'locations' => [],
                    'minAnnualSalary' => null,
                    'minDailySalary' => null,
                    'currency' => null,
                    'contracts' => [
                        Contract::CONTRACTOR,
                    ],
                    'minDuration' => null,
                    'maxDuration' => null,
                    'remoteMode' => [
                        RemoteMode::FULL,
                    ],
                    'publishedSince' => PublishedSince::FROM_1_TO_7_DAYS,
                    'user' => '/users/6',
                ],
                [
                    '@type' => 'JobPostingSearch',
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
                    'minAnnualSalary' => null,
                    'minDailySalary' => null,
                    'currency' => null,
                    'contracts' => [
                        Contract::PERMANENT,
                        Contract::FIXED_TERM,
                    ],
                    'minDuration' => null,
                    'maxDuration' => null,
                    'remoteMode' => null,
                    'publishedSince' => PublishedSince::LESS_THAN_24_HOURS,
                    'user' => '/users/6',
                ],
            ],
            'hydra:totalItems' => 3,
        ]);
    }
}
