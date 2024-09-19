<?php

namespace App\Folder\Tests\Functional\Folder;

use App\Tests\Functional\ApiTestCase;

class FoldersGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/folders');
        self::assertResponseStatusCodeSame(401);
    }

    public static function provideLoggedCases(): iterable
    {
        yield [
            'email' => 'walter.white@breaking-bad.com',
            'parameters' => [],
            'expected' => [
                '@context' => '/contexts/Folder',
                '@id' => '/folders',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    [
                        '@id' => '/folders/1',
                        '@type' => 'Folder',
                        'id' => 1,
                        'name' => null,
                        'type' => 'viewed',
                        'usersCount' => 4,
                    ],
                    [
                        '@id' => '/folders/2',
                        '@type' => 'Folder',
                        'id' => 2,
                        'name' => null,
                        'type' => 'cart',
                        'usersCount' => 3,
                    ],
                ],
                'hydra:totalItems' => 9,
                'hydra:view' => [
                    '@id' => '/folders?page=1',
                    'hydra:first' => '/folders?page=1',
                    'hydra:last' => '/folders?page=5',
                    'hydra:next' => '/folders?page=2',
                ],
                'hydra:search' => [
                    '@type' => 'hydra:IriTemplate',
                    'hydra:template' => '/folders{?name,type,type[]}',
                    'hydra:variableRepresentation' => 'BasicRepresentation',
                    'hydra:mapping' => [
                        [
                            '@type' => 'IriTemplateMapping',
                            'variable' => 'name',
                            'property' => 'name',
                            'required' => false,
                        ],
                        [
                            '@type' => 'IriTemplateMapping',
                            'variable' => 'type',
                            'property' => 'type',
                            'required' => false,
                        ],
                        [
                            '@type' => 'IriTemplateMapping',
                            'variable' => 'type[]',
                            'property' => 'type',
                            'required' => false,
                        ],
                    ],
                ],
            ],
        ];

        yield [
            'email' => 'walter.white@breaking-bad.com',
            'parameters' => [
                'name' => 'recherche',
            ],
            'expected' => [
                '@context' => '/contexts/Folder',
                '@id' => '/folders',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    [
                        '@id' => '/folders/120',
                        '@type' => 'Folder',
                        'id' => 120,
                        'name' => 'Recherche PHP',
                        'type' => 'personal',
                        'usersCount' => 0,
                    ],
                    [
                        '@id' => '/folders/121',
                        '@type' => 'Folder',
                        'id' => 121,
                        'name' => 'Recherche Java',
                        'type' => 'personal',
                        'usersCount' => 0,
                    ],
                ],
                'hydra:totalItems' => 2,
                'hydra:search' => [
                    '@type' => 'hydra:IriTemplate',
                    'hydra:template' => '/folders{?name,type,type[]}',
                    'hydra:variableRepresentation' => 'BasicRepresentation',
                    'hydra:mapping' => [
                        [
                            '@type' => 'IriTemplateMapping',
                            'variable' => 'name',
                            'property' => 'name',
                            'required' => false,
                        ],
                        [
                            '@type' => 'IriTemplateMapping',
                            'variable' => 'type',
                            'property' => 'type',
                            'required' => false,
                        ],
                        [
                            '@type' => 'IriTemplateMapping',
                            'variable' => 'type[]',
                            'property' => 'type',
                            'required' => false,
                        ],
                    ],
                ],
            ],
        ];

        yield [
            'email' => 'walter.white@breaking-bad.com',
            'parameters' => [
                'type' => 'favorites',
            ],
            'expected' => [
                '@context' => '/contexts/Folder',
                '@id' => '/folders',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    [
                        '@id' => '/folders/4',
                        '@type' => 'Folder',
                        'id' => 4,
                        'name' => null,
                        'type' => 'favorites',
                        'usersCount' => 3,
                    ],
                ],
                'hydra:totalItems' => 1,
                'hydra:search' => [
                    '@type' => 'hydra:IriTemplate',
                    'hydra:template' => '/folders{?name,type,type[]}',
                    'hydra:variableRepresentation' => 'BasicRepresentation',
                    'hydra:mapping' => [
                        [
                            '@type' => 'IriTemplateMapping',
                            'variable' => 'name',
                            'property' => 'name',
                            'required' => false,
                        ],
                        [
                            '@type' => 'IriTemplateMapping',
                            'variable' => 'type',
                            'property' => 'type',
                            'required' => false,
                        ],
                        [
                            '@type' => 'IriTemplateMapping',
                            'variable' => 'type[]',
                            'property' => 'type',
                            'required' => false,
                        ],
                    ],
                ],
            ],
        ];

        yield [
            'email' => 'arya.stark@got.com',
            'parameters' => [
                'name' => 'développeurs',
                'type' => 'personal',
            ],
            'expected' => [
                '@context' => '/contexts/Folder',
                '@id' => '/folders',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    [
                        '@id' => '/folders/122',
                        '@type' => 'Folder',
                        'id' => 122,
                        'name' => 'Développeurs Vue.js',
                        'type' => 'personal',
                        'usersCount' => 1,
                    ],
                ],
                'hydra:totalItems' => 1,
                'hydra:search' => [
                    '@type' => 'hydra:IriTemplate',
                    'hydra:template' => '/folders{?name,type,type[]}',
                    'hydra:variableRepresentation' => 'BasicRepresentation',
                    'hydra:mapping' => [
                        [
                            '@type' => 'IriTemplateMapping',
                            'variable' => 'name',
                            'property' => 'name',
                            'required' => false,
                        ],
                        [
                            '@type' => 'IriTemplateMapping',
                            'variable' => 'type',
                            'property' => 'type',
                            'required' => false,
                        ],
                        [
                            '@type' => 'IriTemplateMapping',
                            'variable' => 'type[]',
                            'property' => 'type',
                            'required' => false,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideLoggedCases
     */
    public function testLogged(string $email, array $parameters, array $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient($email);

        $path = '/folders?' . http_build_query($parameters);
        $client->request('GET', $path);

        self::assertResponseIsSuccessful();
        self::assertJsonContains($expected);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}
