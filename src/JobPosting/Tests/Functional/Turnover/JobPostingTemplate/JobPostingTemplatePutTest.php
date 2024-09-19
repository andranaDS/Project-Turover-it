<?php

namespace App\JobPosting\Tests\Functional\Turnover\JobPostingTemplate;

use App\Tests\Functional\ApiTestCase;

class JobPostingTemplatePutTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('PUT', '/job_posting_templates/2', [
            'json' => [
                'title' => 'test put',
            ],
        ]);

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

    public function testBadRequest(): void
    {
        $client = static::createTurnoverAuthenticatedClient('eddard.stark@got.com');
        $client->request('PUT', '/job_posting_templates/1', [
            'json' => [
                'title' => 98876,
                'minDailySalary' => 'string',
            ],
        ]);
        self::assertResponseStatusCodeSame(400);
        self::assertJsonContains(
            [
                '@context' => '/contexts/Error',
                '@type' => 'hydra:Error',
                'hydra:title' => 'An error occurred',
                'hydra:description' => 'The type of the "title" attribute must be "string", "integer" given.',
            ]
        );
    }

    public function testWithRecruiterNotOwner(): void
    {
        $client = static::createTurnoverAuthenticatedClient('robb.stark@got.com');
        $client->request('PUT', '/job_posting_templates/2', ['json' => [
            'title' => 'test put',
        ]]);
        self::assertResponseStatusCodeSame(403);
        self::assertJsonContains(
            [
                '@context' => '/contexts/Error',
                '@type' => 'hydra:Error',
                'hydra:title' => 'An error occurred',
                'hydra:description' => 'Access Denied.',
            ]
        );
    }

    public function testUpdateTemplate(): void
    {
        $client = static::createTurnoverAuthenticatedClient('eddard.stark@got.com');
        $client->request('PUT', '/job_posting_templates/1', [
            'json' => [
                'title' => 'Test put',
                'contracts' => ['permanent', 'internship'],
            ],
        ]);
        self::assertResponseIsSuccessful();

        self::assertJsonContains([
            '@context' => '/contexts/JobPostingTemplate',
            '@id' => '/job_posting_templates/1',
            '@type' => 'JobPostingTemplate',
            'id' => 1,
            'title' => 'Test put',
            'contracts' => ['permanent', 'internship'],
        ]);
    }

    public function testItemNotFound(): void
    {
        $client = static::createTurnoverAuthenticatedClient('eddard.stark@got.com');
        $client->request('PUT', '/job_posting_templates/not-fount', [
            'json' => [
                'title' => 'Test put',
            ],
        ]);
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

    public function testInvalidPayload(): void
    {
        $client = static::createTurnoverAuthenticatedClient('eddard.stark@got.com');

        $client->request('PUT', '/job_posting_templates/1', [
            'json' => [
                'title' => 'Test put',
                'contracts' => ['invalid-contract'],
            ],
        ]);
        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains(
            [
                '@context' => '/contexts/ConstraintViolationList',
                '@type' => 'ConstraintViolationList',
                'hydra:title' => 'An error occurred',
                'hydra:description' => 'contracts: Une ou plusieurs des valeurs soumises sont invalides.',
                'violations' => [
                    [
                        'propertyPath' => 'contracts',
                        'message' => 'Une ou plusieurs des valeurs soumises sont invalides.',
                    ],
                ],
            ]
        );
    }
}
