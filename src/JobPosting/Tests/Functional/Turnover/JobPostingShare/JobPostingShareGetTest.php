<?php

namespace App\JobPosting\Tests\Functional\Turnover\JobPostingShare;

use App\Tests\Functional\ApiTestCase;

class JobPostingShareGetTest extends ApiTestCase
{
    public function testNotFound(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/job_posting_shares/not-found');

        self::assertResponseStatusCodeSame(404);
    }

    public function testLoggedOnOtherResource(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/job_posting_shares/1');

        self::assertResponseStatusCodeSame(404);
    }

    public function testLoggedOnMyResource(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/job_posting_shares/6');

        self::assertResponseStatusCodeSame(404);
    }
}
