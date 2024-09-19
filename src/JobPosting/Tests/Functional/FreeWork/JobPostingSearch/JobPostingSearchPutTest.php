<?php

namespace App\JobPosting\Tests\Functional\FreeWork\JobPostingSearch;

use App\JobPosting\Enum\Contract;
use App\JobPosting\Enum\PublishedSince;
use App\JobPosting\Enum\RemoteMode;
use App\Tests\Functional\ApiTestCase;
use Symfony\Component\String\ByteString;

class JobPostingSearchPutTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('PUT', '/job_posting_searches/1', [
            'json' => [
                'title' => 'JobPostingSearch 1 - User 6 - Updated',
                'contracts' => [Contract::FIXED_TERM],
                'activeAlert' => false,
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsNotOwner(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('vincent.van-gogh@free-work.fr');
        $client->request('PUT', '/job_posting_searches/1', [
            'json' => [
                'title' => 'JobPostingSearch 1 - User 6 - Updated',
                'contracts' => [Contract::FIXED_TERM],
                'activeAlert' => false,
            ],
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsOwner(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('PUT', '/job_posting_searches/1', [
            'json' => [
                'title' => 'JobPostingSearch 1 - User 6 - Updated',
                'contracts' => [Contract::FIXED_TERM],
                'activeAlert' => false,
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('PUT', '/job_posting_searches/1', [
            'json' => [
                'title' => 'JobPostingSearch 1 - User 6 - Updated',
                'contracts' => [Contract::FIXED_TERM],
                'activeAlert' => false,
            ],
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    public function testWithValidData(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('PUT', '/job_posting_searches/1', [
            'json' => [
                'title' => 'JobPostingSearch 1 - User 6 - Updated',
                'searchKeywords' => 'JobPostingSearch 3 - User 6 - keywords - Updated',
                'remoteMode' => [
                    RemoteMode::FULL,
                    RemoteMode::PARTIAL,
                ],
                'publishedSince' => PublishedSince::FROM_1_TO_7_DAYS,
                'contracts' => [
                    Contract::FIXED_TERM,
                    Contract::CONTRACTOR,
                ],
                'minAnnualSalary' => 15000,
                'minDailySalary' => null,
                'minDuration' => 30,
                'maxDuration' => null,
                'activeAlert' => false,
                'locationKeys' => [
                    'fr~nouvelle-aquitaine~~bordeaux',
                    'fr~ile-de-france~~paris',
                ],
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/JobPostingSearch',
            '@type' => 'JobPostingSearch',
            'title' => 'JobPostingSearch 1 - User 6 - Updated',
            'searchKeywords' => 'JobPostingSearch 3 - User 6 - keywords - Updated',
            'remoteMode' => [
                RemoteMode::FULL,
                RemoteMode::PARTIAL,
            ],
            'publishedSince' => PublishedSince::FROM_1_TO_7_DAYS,
            'contracts' => [
                'fixed-term',
                'contractor',
            ],
            'minAnnualSalary' => 15000,
            'minDailySalary' => null,
            'currency' => null,
            'minDuration' => 30,
            'maxDuration' => null,
            'activeAlert' => false,
            'user' => '/users/6',
            'locations' => [
                [
                    '@type' => 'JobPostingSearchLocation',
                    'location' => [
                        '@type' => 'Location',
                        'street' => null,
                        'locality' => 'Bordeaux',
                        'postalCode' => '33000',
                        'adminLevel1' => 'Nouvelle-Aquitaine',
                        'adminLevel2' => null,
                        'country' => 'France',
                        'countryCode' => 'FR',
                        'latitude' => '44.841225',
                        'longitude' => '-0.5800364',
                    ],
                ],
                [
                    '@type' => 'JobPostingSearchLocation',
                    'location' => [
                        '@type' => 'Location',
                        'street' => null,
                        'locality' => 'Paris',
                        'postalCode' => '75000',
                        'adminLevel1' => 'Île-de-France',
                        'adminLevel2' => null,
                        'country' => 'France',
                        'countryCode' => 'FR',
                        'latitude' => '48.8566969',
                        'longitude' => '2.3514616',
                    ],
                ],
            ],
        ]);
    }

    public function testWithInvalidData(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('PUT', '/job_posting_searches/1', [
            'json' => [
                'title' => ByteString::fromRandom(256),
                'contracts' => ['Invalid_contract'],
                'remoteMode' => ['Invalid_remoteMode'],
                'publishedSince' => 'Invalid_publishedSince',
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'violations' => [
                [
                    'propertyPath' => 'title',
                    'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 255 caractères.',
                ],
                [
                    'propertyPath' => 'remoteMode',
                    'message' => 'Une ou plusieurs des valeurs soumises sont invalides.',
                ],
                [
                    'propertyPath' => 'publishedSince',
                    'message' => 'Cette valeur doit être l\'un des choix proposés.',
                ],
                [
                    'propertyPath' => 'contracts',
                    'message' => 'Une ou plusieurs des valeurs soumises sont invalides.',
                ],
            ],
        ]);
    }

    public function testWithMissingData(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('PUT', '/job_posting_searches/1', [
            'json' => [
                'title' => '',
                'contracts' => [],
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'violations' => [
                [
                    'propertyPath' => 'title',
                    'message' => 'Cette valeur ne doit pas être vide.',
                ],
            ],
        ]);
    }

    public function testActiveJobPostingSearchesCountWithInactive(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $client->request('GET', '/users/me');
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'activeJobPostingSearchesCount' => 2,
        ]);

        $client->request('PUT', '/job_posting_searches/1', [
            'json' => [
                'activeAlert' => false,
            ],
        ]);
        self::assertResponseIsSuccessful();

        $client->request('GET', '/users/me');
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'activeJobPostingSearchesCount' => 1,
        ]);
    }

    public function testActiveJobPostingSearchesCountWithActive(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $client->request('GET', '/users/me');
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'activeJobPostingSearchesCount' => 2,
        ]);

        $client->request('PUT', '/job_posting_searches/5', [
            'json' => [
                'activeAlert' => true,
            ],
        ]);
        self::assertResponseIsSuccessful();

        $client->request('GET', '/users/me');
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'activeJobPostingSearchesCount' => 3,
        ]);
    }
}
