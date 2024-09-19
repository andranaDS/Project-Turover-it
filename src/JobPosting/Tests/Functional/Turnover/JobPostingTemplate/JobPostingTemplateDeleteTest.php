<?php

namespace App\JobPosting\Tests\Functional\Turnover\JobPostingTemplate;

use App\Tests\Functional\ApiTestCase;

class JobPostingTemplateDeleteTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/job_posting_templates/1');
        self::assertResponseStatusCodeSame(401);
    }

    public function testNotFound(): void
    {
        $client = static::createTurnoverAuthenticatedClient('eddard.stark@got.com');

        $client->request('GET', '/job_posting_templates/not-found');
        self::assertResponseStatusCodeSame(404);
    }

    public function testDeleteTemplateWithRecruiterNotOwner(): void
    {
        $client = static::createTurnoverAuthenticatedClient('eddard.stark@got.com');
        $client->request('DELETE', '/job_posting_templates/5');
        self::assertResponseStatusCodeSame(403);
    }

    public function testDeleteTemplate(): void
    {
        $client = static::createTurnoverAuthenticatedClient('eddard.stark@got.com');

        $client->request('DELETE', '/job_posting_templates/1');
        self::assertResponseStatusCodeSame(204);

        $client->request('DELETE', '/job_posting_templates/1');
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
}
