<?php

namespace App\JobPosting\Tests\Functional\Turnover\JobPostingSearchRecruiterFavorite;

use App\Tests\Functional\ApiTestCase;

class JobPostingSearchRecruiterFavoriteDeleteTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('DELETE', '/job_posting_search_recruiter_favorites/1');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('DELETE', '/job_posting_search_recruiter_favorites/3');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsOwner(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('DELETE', '/job_posting_search_recruiter_favorites/1');

        self::assertResponseStatusCodeSame(204);
    }

    public function testDelete(): void
    {
        $client = static::createTurnoverAuthenticatedClient();

        $client->request('GET', '/job_posting_search_recruiter_favorites/1');
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/contexts/JobPostingSearchRecruiterFavorite',
            '@type' => 'JobPostingSearchRecruiterFavorite',
            '@id' => '/job_posting_search_recruiter_favorites/1',
        ]);

        $client->request('DELETE', '/job_posting_search_recruiter_favorites/1');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/job_posting_search_recruiter_favorites/1');
        self::assertResponseStatusCodeSame(404);
    }
}
