<?php

namespace App\JobPosting\Tests\Functional\Turnover\JobPostingUserTrace;

use App\Tests\Functional\ApiTestCase;

class JobPostingRecruiterTracePostTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('POST', '/job_postings/5/trace');

        self::assertResponseStatusCodeSame(401);
        self::assertJsonContains(
            [
                '@context' => '/contexts/Error',
                '@type' => 'hydra:Error',
                'hydra:title' => 'An error occurred',
                'hydra:description' => 'Full authentication is required to access this resource.',
            ]
        );
    }

    public function testNotFound(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('POST', '/job_postings/101/trace');
        self::assertResponseStatusCodeSame(404);
        self::assertJsonContains(
            [
                '@context' => '/contexts/Error',
                '@type' => 'hydra:Error',
                'hydra:title' => 'An error occurred',
                'hydra:description' => 'Not Found',
            ]
        );
    }

    public function testCreateTraceLogged(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('POST', '/job_postings/5/trace');

        self::assertResponseStatusCodeSame(201);
    }
}
