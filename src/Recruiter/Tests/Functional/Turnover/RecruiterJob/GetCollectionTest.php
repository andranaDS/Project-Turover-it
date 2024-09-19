<?php

namespace App\Recruiter\Tests\Functional\Turnover\RecruiterJob;

use App\Tests\Functional\ApiTestCase;

class GetCollectionTest extends ApiTestCase
{
    public static function provideCollectionCases(): iterable
    {
        return [
            'Without filter' => [
                '/recruiter_jobs',
                [
                    '@context' => '/contexts/RecruiterJob',
                    '@id' => '/recruiter_jobs',
                    '@type' => 'hydra:Collection',
                    'hydra:member' => [
                        [
                            '@id' => '/recruiter_jobs/1',
                            '@type' => 'RecruiterJob',
                            'id' => 1,
                            'name' => 'Account manager',
                        ],
                        [
                            '@id' => '/recruiter_jobs/2',
                            '@type' => 'RecruiterJob',
                            'id' => 2,
                            'name' => 'Architecte',
                        ],
                    ],
                    'hydra:totalItems' => 78,
                    'hydra:view' => [
                        '@id' => '/recruiter_jobs?page=1',
                        '@type' => 'hydra:PartialCollectionView',
                        'hydra:first' => '/recruiter_jobs?page=1',
                        'hydra:last' => '/recruiter_jobs?page=2',
                        'hydra:next' => '/recruiter_jobs?page=39',
                    ],
                    'hydra:search' => [
                        '@type' => 'hydra:IriTemplate',
                        'hydra:template' => '/recruiter_jobs{?name}',
                        'hydra:variableRepresentation' => 'BasicRepresentation',
                        'hydra:mapping' => [
                            [
                                '@type' => 'IriTemplateMapping',
                                'variable' => 'name',
                                'property' => 'name',
                                'required' => false,
                            ],
                        ],
                    ],
                ],
            ],
            'With filter and itemsCount > 2' => [
                '/recruiter_jobs?name=recrut',
                [
                    '@context' => '/contexts/RecruiterJob',
                    '@id' => '/recruiter_jobs',
                    '@type' => 'hydra:Collection',
                    'hydra:member' => [
                        [
                            '@id' => '/recruiter_jobs/13',
                            '@type' => 'RecruiterJob',
                            'id' => 13,
                            'name' => 'Chargé de recrutement',
                        ],
                        [
                            '@id' => '/recruiter_jobs/23',
                            '@type' => 'RecruiterJob',
                            'id' => 23,
                            'name' => 'Consultant en recrutement',
                        ],
                    ],
                    'hydra:totalItems' => 4,
                    'hydra:view' => [
                        '@id' => '/recruiter_jobs?name=recrut&page=1',
                        '@type' => 'hydra:PartialCollectionView',
                        'hydra:first' => '/recruiter_jobs?name=recrut&page=1',
                        'hydra:last' => '/recruiter_jobs?name=recrut&page=2',
                        'hydra:next' => '/recruiter_jobs?name=recrut&page=2',
                    ],
                    'hydra:search' => [
                        '@type' => 'hydra:IriTemplate',
                        'hydra:template' => '/recruiter_jobs{?name}',
                        'hydra:variableRepresentation' => 'BasicRepresentation',
                        'hydra:mapping' => [
                            [
                                '@type' => 'IriTemplateMapping',
                                'variable' => 'name',
                                'property' => 'name',
                                'required' => false,
                            ],
                        ],
                    ],
                ],
            ],
            'With filter and itemsCount < 2' => [
                '/recruiter_jobs?name=senior',
                [
                    '@context' => '/contexts/RecruiterJob',
                    '@id' => '/recruiter_jobs',
                    '@type' => 'hydra:Collection',
                    'hydra:member' => [
                        [
                            '@id' => '/recruiter_jobs/69',
                            '@type' => 'RecruiterJob',
                            'id' => 69,
                            'name' => 'Senior recruiter',
                        ],
                    ],
                    'hydra:totalItems' => 1,
                    'hydra:view' => [
                        '@id' => '/recruiter_jobs?name=senior',
                        '@type' => 'hydra:PartialCollectionView',
                    ],
                    'hydra:search' => [
                        '@type' => 'hydra:IriTemplate',
                        'hydra:template' => '/recruiter_jobs{?name}',
                        'hydra:variableRepresentation' => 'BasicRepresentation',
                        'hydra:mapping' => [
                            [
                                '@type' => 'IriTemplateMapping',
                                'variable' => 'name',
                                'property' => 'name',
                                'required' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideCollectionCases
     */
    public function testCollection(string $query, array $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', $query);

        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertResponseStatusCodeSame(200);
        self::assertJsonEquals($expected);
    }
}
