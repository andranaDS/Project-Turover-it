<?php

namespace App\JobPosting\Tests\Functional\Turnover\JobPostingSearchRecruiterAlert;

use App\Tests\Functional\ApiTestCase;

class JobPostingSearchRecruiterAlertDeleteTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('DELETE', '/job_posting_search_recruiter_alerts/1');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('DELETE', '/job_posting_search_recruiter_alerts/3');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsOwner(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('DELETE', '/job_posting_search_recruiter_alerts/1');

        self::assertResponseStatusCodeSame(204);
    }

    public function testDelete(): void
    {
        $client = static::createTurnoverAuthenticatedClient();

        $client->request('GET', '/job_posting_search_recruiter_alerts/1');
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/contexts/JobPostingSearchRecruiterAlert',
            '@type' => 'JobPostingSearchRecruiterAlert',
            '@id' => '/job_posting_search_recruiter_alerts/1',
        ]);

        $client->request('DELETE', '/job_posting_search_recruiter_alerts/1');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/job_posting_search_recruiter_alerts/1');
        self::assertResponseStatusCodeSame(404);
    }
}
