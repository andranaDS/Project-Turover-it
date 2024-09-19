<?php

namespace App\JobPosting\Tests\Functional\Turnover\JobPostingSearchRecruiterLog;

use App\Tests\Functional\ApiTestCase;

class JobPostingSearchRecruiterLogGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/job_posting_search_recruiter_logs/1');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/job_posting_search_recruiter_logs/4');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsOwner(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/job_posting_search_recruiter_logs/1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testWithData(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/job_posting_search_recruiter_logs/2');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/JobPostingSearchRecruiterLog',
            '@type' => 'JobPostingSearchRecruiterLog',
            'keywords' => 'Job Posting Recruiter 1 Search 2',
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
                    '@type' => 'JobPostingSearchRecruiterLogLocation',
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
                    '@type' => 'JobPostingSearchRecruiterLogLocation',
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
        ]);
    }

    public function testWithInvalidJobPostingSearchId(): void
    {
        $client = static::createTurnoverAuthenticatedClient();

        $client->request('GET', '/job_posting_search_recruiter_logs/1337');
        self::assertResponseStatusCodeSame(404);
    }
}
