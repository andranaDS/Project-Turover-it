<?php

namespace App\JobPosting\Tests\Functional\Turnover\JobPostingSearchRecruiterFavorite;

use App\Tests\Functional\ApiTestCase;

class JobPostingSearchRecruiterFavoriteGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/job_posting_search_recruiter_favorites/1');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/job_posting_search_recruiter_favorites/3');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsOwner(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/job_posting_search_recruiter_favorites/1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testWithData(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/job_posting_search_recruiter_favorites/1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/JobPostingSearchRecruiterFavorite',
            '@type' => 'JobPostingSearchRecruiterFavorite',
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
        ]);
    }

    public function testWithInvalidJobPostingSearchId(): void
    {
        $client = static::createTurnoverAuthenticatedClient();

        $client->request('GET', '/job_posting_search_recruiter_favorites/1337');
        self::assertResponseStatusCodeSame(404);
    }
}
