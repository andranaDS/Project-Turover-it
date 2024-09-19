<?php

namespace App\JobPosting\Tests\Functional\FreeWork\Application;

use App\Tests\Functional\ApiTestCase;

class ApplicationsLegacyGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/legacy/applications');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/legacy/applications');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('GET', '/legacy/applications');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsTurnover(): void
    {
        $client = static::createFreeWorkClient();

        $client->request('GET', '/legacy/applications', [
            'headers' => [
                'X-AUTH-TOKEN' => $_ENV['TURNOVER_IT_API_KEY'],
            ],
        ]);

        self::assertResponseStatusCodeSame(200);
        self::assertJsonContains([
            '@context' => '/contexts/Application',
            '@id' => '/legacy/applications',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/applications/1',
                    '@type' => 'Application',
                    'id' => 1,
                    'step' => 'resume',
                    'state' => [
                        'value' => 'in_progress',
                        'label' => 'Candidature en cours',
                    ],
                    'content' => 'Job 1 - Application 1',
                    'documents' => [],
                    'jobPosting' => [
                        '@id' => '/job_postings/1',
                        '@type' => 'JobPosting',
                        'oldId' => 10001,
                    ],
                    'company' => null,
                    'user' => [
                        '@id' => '/users/6',
                        '@type' => 'User',
                        'id' => 6,
                        'email' => 'claude.monet@free-work.fr',
                    ],
                    'createdAtTimestamp' => 1609491600,
                    'updatedAtTimestamp' => 1609578000,
                ],
                [
                    '@id' => '/applications/2',
                    '@type' => 'Application',
                    'id' => 2,
                    'step' => 'seen',
                    'state' => [
                        'value' => 'in_progress',
                        'label' => 'Candidature en cours',
                    ],
                    'content' => 'Job 2 - Application 1',
                    'documents' => [],
                    'jobPosting' => [
                        '@id' => '/job_postings/2',
                        '@type' => 'JobPosting',
                        'oldId' => 10002,
                    ],
                    'company' => null,
                    'user' => [
                        '@id' => '/users/6',
                        '@type' => 'User',
                        'id' => 6,
                        'email' => 'claude.monet@free-work.fr',
                    ],
                    'createdAtTimestamp' => 1609578000,
                    'updatedAtTimestamp' => 1609664400,
                ],
            ],
            'hydra:totalItems' => 7,
            'hydra:view' => [
                '@id' => '/legacy/applications?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/legacy/applications?page=1',
                'hydra:last' => '/legacy/applications?page=4',
                'hydra:next' => '/legacy/applications?page=2',
            ],
            'hydra:search' => [
                '@type' => 'hydra:IriTemplate',
                'hydra:template' => '/legacy/applications{?step,step[],state,state[],createdAt[before],createdAt[strictly_before],createdAt[after],createdAt[strictly_after],updatedAt[before],updatedAt[strictly_before],updatedAt[after],updatedAt[strictly_after],properties[]}',
                'hydra:variableRepresentation' => 'BasicRepresentation',
                'hydra:mapping' => [
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'step',
                        'property' => 'step',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'step[]',
                        'property' => 'step',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'state',
                        'property' => 'state',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'state[]',
                        'property' => 'state',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'createdAt[before]',
                        'property' => 'createdAt',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'createdAt[strictly_before]',
                        'property' => 'createdAt',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'createdAt[after]',
                        'property' => 'createdAt',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'createdAt[strictly_after]',
                        'property' => 'createdAt',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'updatedAt[before]',
                        'property' => 'updatedAt',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'updatedAt[strictly_before]',
                        'property' => 'updatedAt',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'updatedAt[after]',
                        'property' => 'updatedAt',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'updatedAt[strictly_after]',
                        'property' => 'updatedAt',
                        'required' => false,
                    ],
                ],
            ],
        ]);
    }
}
