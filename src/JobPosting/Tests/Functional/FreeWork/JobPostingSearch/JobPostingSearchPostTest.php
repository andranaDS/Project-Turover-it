<?php

namespace App\JobPosting\Tests\Functional\FreeWork\JobPostingSearch;

use App\JobPosting\Enum\Contract;
use App\JobPosting\Enum\PublishedSince;
use App\JobPosting\Enum\RemoteMode;
use App\Tests\Functional\ApiTestCase;
use Symfony\Component\String\ByteString;

class JobPostingSearchPostTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('POST', '/job_posting_searches', [
            'json' => [
                'title' => 'JobPostingSearch 1 - User',
                'contracts' => [Contract::FIXED_TERM],
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testWithValidData(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('POST', '/job_posting_searches', [
            'json' => [
                'title' => 'JobPostingSearch 3 - User 6',
                'searchKeywords' => 'JobPostingSearch 3 - User 6 - keywords',
                'remoteMode' => [
                    RemoteMode::FULL,
                    RemoteMode::PARTIAL,
                ],
                'publishedSince' => PublishedSince::FROM_1_TO_7_DAYS,
                'contracts' => [
                    Contract::PERMANENT,
                    Contract::CONTRACTOR,
                ],
                'minAnnualSalary' => 15000,
                'minDailySalary' => 300,
                'minDuration' => 30,
                'maxDuration' => null,
                'activeAlert' => true,
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
            'title' => 'JobPostingSearch 3 - User 6',
            'searchKeywords' => 'JobPostingSearch 3 - User 6 - keywords',
            'remoteMode' => [
                RemoteMode::FULL,
                RemoteMode::PARTIAL,
            ],
            'publishedSince' => PublishedSince::FROM_1_TO_7_DAYS,
            'contracts' => [
                'permanent',
                'contractor',
            ],
            'minAnnualSalary' => 15000,
            'minDailySalary' => 300,
            'currency' => null,
            'minDuration' => 30,
            'maxDuration' => null,
            'activeAlert' => true,
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
                        'key' => 'fr~nouvelle-aquitaine~~bordeaux',
                        'label' => 'Bordeaux, Nouvelle-Aquitaine',
                        'shortLabel' => 'Bordeaux (33)',
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
                        'key' => 'fr~ile-de-france~~paris',
                        'label' => 'Paris, Île-de-France',
                        'shortLabel' => 'Paris (75)',
                    ],
                ],
            ],
            'annualSalary' => "15k\u{a0}€",
            'dailySalary' => "300\u{a0}€",
        ]);
    }

    public function testWithInvalidData(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('POST', '/job_posting_searches', [
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
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('POST', '/job_posting_searches', [
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

    public function testActiveJobPostingSearchesCountWithActive(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $client->request('GET', '/users/me');
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'activeJobPostingSearchesCount' => 2,
        ]);

        $client->request('POST', '/job_posting_searches', [
            'json' => [
                'title' => 'JobPostingSearch 3 - User 6',
                'searchKeywords' => 'JobPostingSearch 3 - User 6 - keywords',
                'remoteMode' => [
                    RemoteMode::FULL,
                    RemoteMode::PARTIAL,
                ],
                'publishedSince' => PublishedSince::FROM_1_TO_7_DAYS,
                'contracts' => [
                    Contract::PERMANENT,
                    Contract::CONTRACTOR,
                ],
                'minAnnualSalary' => 15000,
                'minDailySalary' => 300,
                'minDuration' => 30,
                'maxDuration' => null,
                'activeAlert' => true,
                'locationKeys' => [
                    'fr~nouvelle-aquitaine~~bordeaux',
                    'fr~ile-de-france~~paris',
                ],
            ],
        ]);

        $client->request('GET', '/users/me');
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'activeJobPostingSearchesCount' => 3,
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

        $client->request('POST', '/job_posting_searches', [
            'json' => [
                'title' => 'JobPostingSearch 3 - User 6',
                'searchKeywords' => 'JobPostingSearch 3 - User 6 - keywords',
                'remoteMode' => [
                    RemoteMode::FULL,
                    RemoteMode::PARTIAL,
                ],
                'publishedSince' => PublishedSince::FROM_1_TO_7_DAYS,
                'contracts' => [
                    Contract::PERMANENT,
                    Contract::CONTRACTOR,
                ],
                'minAnnualSalary' => 15000,
                'minDailySalary' => 300,
                'minDuration' => 30,
                'maxDuration' => null,
                'activeAlert' => false,
                'locationKeys' => [
                    'fr~nouvelle-aquitaine~~bordeaux',
                    'fr~ile-de-france~~paris',
                ],
            ],
        ]);

        $client->request('GET', '/users/me');
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'activeJobPostingSearchesCount' => 2,
        ]);
    }
}
