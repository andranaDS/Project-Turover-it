<?php

namespace App\Recruiter\Tests\Functional\Turnover\Recruiter;

use App\Tests\Functional\ApiTestCase;

class GetJobPostingSearchAlertsTest extends ApiTestCase
{
    public function testNotLoggedCases(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/recruiters/me/job_posting_search_alerts');

        self::assertResponseStatusCodeSame(401);
    }

    public function testValidCases(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/recruiters/me/job_posting_search_alerts');

        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/JobPostingSearchRecruiterAlert',
            '@id' => '/job_posting_search_recruiter_alerts',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    'title' => 'Job Posting Recruiter 1 Alert 2 title',
                    'keywords' => 'Job Posting Recruiter 1 Alert 2',
                    'remoteMode' => ['partial'],
                    'publishedSince' => 'from_1_to_7_days',
                    'minDailySalary' => 350,
                    'maxDailySalary' => 650,
                    'currency' => 'EUR',
                    'minDuration' => null,
                    'maxDuration' => 360,
                    'intercontractOnly' => true,
                    'startsAt' => (new \DateTime())->modify('+30 days')->setTime(8, 30)->format(\DateTime::RFC3339),
                    'businessActivity' => [
                        '@id' => '/company_business_activities/2',
                        '@type' => 'CompanyBusinessActivity',
                        'id' => 2,
                        'name' => 'Business activity 2',
                        'slug' => 'business-activity-2',
                    ],
                    'active' => true,
                    'locations' => [
                        [
                            '@type' => 'JobPostingSearchRecruiterAlertLocation',
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
                ],
                [
                    'title' => 'Job Posting Recruiter 1 Alert 1 title',
                    'keywords' => 'Job Posting Recruiter 1 Alert 1',
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
                    'active' => true,
                    'locations' => [],
                ],
            ],
            'hydra:totalItems' => 2,
        ]);
    }
}
