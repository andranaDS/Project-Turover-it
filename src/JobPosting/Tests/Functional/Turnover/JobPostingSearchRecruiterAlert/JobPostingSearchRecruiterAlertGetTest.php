<?php

namespace App\JobPosting\Tests\Functional\Turnover\JobPostingSearchRecruiterAlert;

use App\Tests\Functional\ApiTestCase;

class JobPostingSearchRecruiterAlertGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/job_posting_search_recruiter_alerts/1');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/job_posting_search_recruiter_alerts/3');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsOwner(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/job_posting_search_recruiter_alerts/1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testWithData(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/job_posting_search_recruiter_alerts/1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/JobPostingSearchRecruiterAlert',
            '@type' => 'JobPostingSearchRecruiterAlert',
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
        ]);
    }

    public function testWithInvalidJobPostingSearchId(): void
    {
        $client = static::createTurnoverAuthenticatedClient();

        $client->request('GET', '/job_posting_search_recruiter_alerts/1337');
        self::assertResponseStatusCodeSame(404);
    }
}
