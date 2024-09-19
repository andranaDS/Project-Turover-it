<?php

namespace App\JobPosting\Tests\Functional\FreeWork\JobPostingSearch;

use App\Tests\Functional\ApiTestCase;

class JobPostingSearchDeleteTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('DELETE', '/job_posting_searches/1');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('DELETE', '/job_posting_searches/1');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsOwner(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('DELETE', '/job_posting_searches/1');

        self::assertResponseStatusCodeSame(204);
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('DELETE', '/job_posting_searches/1');

        self::assertResponseStatusCodeSame(403);
    }

    public function testDelete(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $client->request('GET', '/job_posting_searches/1');
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/contexts/JobPostingSearch',
            '@type' => 'JobPostingSearch',
            'title' => 'JobPostingSearch 1 - User 6',
        ]);

        $client->request('DELETE', '/job_posting_searches/1');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/job_posting_searches/1');
        self::assertResponseStatusCodeSame(404);
    }

    public function testActiveJobPostingSearchesCount(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $client->request('GET', '/users/me');
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'activeJobPostingSearchesCount' => 2,
        ]);

        $client->request('DELETE', '/job_posting_searches/1');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/users/me');
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'activeJobPostingSearchesCount' => 1,
        ]);
    }
}
