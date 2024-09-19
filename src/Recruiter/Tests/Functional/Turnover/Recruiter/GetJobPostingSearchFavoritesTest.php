<?php

namespace App\Recruiter\Tests\Functional\Turnover\Recruiter;

use App\Tests\Functional\ApiTestCase;

class GetJobPostingSearchFavoritesTest extends ApiTestCase
{
    public function testNotLoggedCases(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/recruiters/me/job_posting_search_favorites');

        self::assertResponseStatusCodeSame(401);
    }

    public function testValidCases(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/recruiters/me/job_posting_search_favorites');

        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/JobPostingSearchRecruiterFavorite',
            '@id' => '/job_posting_search_recruiter_favorites',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    'keywords' => 'Job Posting Recruiter 1 Favorite 2',
                    'remoteMode' => ['full'],
                    'publishedSince' => 'from_1_to_7_days',
                    'minDailySalary' => 150,
                    'maxDailySalary' => 450,
                    'currency' => 'EUR',
                    'minDuration' => 5,
                    'maxDuration' => 90,
                    'intercontractOnly' => false,
                    'startsAt' => (new \DateTime())->modify('+30 days')->setTime(8, 00)->format(\DateTime::RFC3339),
                    'businessActivity' => [
                        '@id' => '/company_business_activities/1',
                        '@type' => 'CompanyBusinessActivity',
                        'id' => 1,
                        'name' => 'Business activity 1',
                        'slug' => 'business-activity-1',
                    ],
                    'locations' => [
                        [
                            '@type' => 'JobPostingSearchRecruiterFavoriteLocation',
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
                        [
                            '@type' => 'JobPostingSearchRecruiterFavoriteLocation',
                            'location' => [
                                '@type' => 'Location',
                                'street' => null,
                                'locality' => 'Lyon',
                                'postalCode' => null,
                                'adminLevel1' => 'Auvergne-Rhône-Alpes',
                                'adminLevel2' => 'Métropole de Lyon',
                                'country' => 'France',
                                'countryCode' => 'FR',
                                'latitude' => '45.7578137',
                                'longitude' => '4.8320114',
                                'key' => 'fr~auvergne-rhone-alpes~metropole-de-lyon~lyon',
                                'label' => 'Lyon, Auvergne-Rhône-Alpes',
                                'shortLabel' => 'Lyon',
                            ],
                        ],
                    ],
                ],
                [
                    'keywords' => 'Job Posting Recruiter 1 Favorite 1',
                    'remoteMode' => ['full'],
                    'publishedSince' => null,
                    'minDailySalary' => 150,
                    'maxDailySalary' => 450,
                    'currency' => 'EUR',
                    'minDuration' => null,
                    'maxDuration' => null,
                    'intercontractOnly' => true,
                    'startsAt' => null,
                    'businessActivity' => [
                        '@id' => '/company_business_activities/1',
                        '@type' => 'CompanyBusinessActivity',
                        'id' => 1,
                        'name' => 'Business activity 1',
                        'slug' => 'business-activity-1',
                    ],
                    'locations' => [],
                ],
            ],
            'hydra:totalItems' => 2,
        ]);
    }
}
