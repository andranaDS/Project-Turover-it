<?php

namespace App\JobPosting\Tests\Functional\Turnover\JobPostingTemplate;

use App\Tests\Functional\ApiTestCase;

class JobPostingTemplatePostTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('POST', '/job_posting_templates', [
            'json' => [
                'title' => 'My template',
                'contracts' => ['permanent', 'internship'],
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
        $client->request('POST', '/job_posting_templates', [
            'json' => [
                'title' => 65433,
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

    public function testCreateTemplate(): void
    {
        $client = static::createTurnoverAuthenticatedClient('eddard.stark@got.com');

        $client->request('POST', '/job_posting_templates', [
            'json' => [
                'title' => 'My template',
                'contracts' => ['permanent', 'internship'],
                'durationValue' => 10,
                'durationPeriod' => 'month',
            ],
        ]);
        self::assertResponseStatusCodeSame(201);
        self::assertJsonContains([
            '@context' => '/contexts/JobPostingTemplate',
            '@type' => 'JobPostingTemplate',
            'createdBy' => [
                '@id' => '/recruiters/4',
                '@type' => 'Recruiter',
                'id' => 4,
            ],
            'title' => 'My template',
            'contracts' => ['permanent', 'internship'],
            'durationValue' => 10,
            'durationPeriod' => 'month',
        ]);
    }

    public function testInvalidInput(): void
    {
        $client = static::createTurnoverAuthenticatedClient('eddard.stark@got.com');

        $client->request('POST', '/job_posting_templates', [
            'json' => [
                'title' => 'My template',
                'contracts' => ['permanent', 'invalid-value'],
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
