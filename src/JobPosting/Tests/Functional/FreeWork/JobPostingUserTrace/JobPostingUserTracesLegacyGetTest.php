<?php

namespace App\JobPosting\Tests\Functional\FreeWork\JobPostingUserTrace;

use App\Tests\Functional\ApiTestCase;

class JobPostingUserTracesLegacyGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/legacy/job_posting_traces');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/legacy/job_posting_traces');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('GET', '/legacy/job_posting_traces');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsTurnover(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/legacy/job_posting_traces', [
            'headers' => [
                'X-AUTH-TOKEN' => $_ENV['TURNOVER_IT_API_KEY'],
            ],
        ]);

        self::assertResponseStatusCodeSame(200);
        self::assertJsonContains([
            '@context' => '/contexts/JobPostingUserTrace',
            '@id' => '/legacy/job_posting_traces',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/job_posting_user_traces/1',
                    '@type' => 'JobPostingUserTrace',
                    'jobPosting' => [
                        '@id' => '/job_postings/1',
                        '@type' => 'JobPosting',
                        'oldId' => 10001,
                    ],
                    'userIdOrIp' => '6',
                    'readAtTimestamp' => 1609504200,
                ],
                [
                    '@id' => '/job_posting_user_traces/2',
                    '@type' => 'JobPostingUserTrace',
                    'jobPosting' => [
                        '@id' => '/job_postings/1',
                        '@type' => 'JobPosting',
                        'oldId' => 10001,
                    ],
                    'userIdOrIp' => '6',
                    'readAtTimestamp' => 1609504500,
                ],
            ],
            'hydra:totalItems' => 4,
            'hydra:view' => [
                '@id' => '/legacy/job_posting_traces?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/legacy/job_posting_traces?page=1',
                'hydra:last' => '/legacy/job_posting_traces?page=2',
                'hydra:next' => '/legacy/job_posting_traces?page=2',
            ],
            'hydra:search' => [
                '@type' => 'hydra:IriTemplate',
                'hydra:template' => '/legacy/job_posting_traces{?readAt[before],readAt[strictly_before],readAt[after],readAt[strictly_after]}',
                'hydra:variableRepresentation' => 'BasicRepresentation',
                'hydra:mapping' => [
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'readAt[before]',
                        'property' => 'readAt',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'readAt[strictly_before]',
                        'property' => 'readAt',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'readAt[after]',
                        'property' => 'readAt',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'readAt[strictly_after]',
                        'property' => 'readAt',
                        'required' => false,
                    ],
                ],
            ],
        ]);
    }
}
