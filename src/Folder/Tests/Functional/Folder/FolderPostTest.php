<?php

namespace App\Folder\Tests\Functional\Folder;

use App\Tests\Functional\ApiTestCase;
use Symfony\Component\String\ByteString;

class FolderPostTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('POST', '/folders', [
            'json' => ['name' => 'Personal folder'],
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public static function provideInvalidCases(): iterable
    {
        yield [
            [],
            [
                '@context' => '/contexts/ConstraintViolationList',
                '@type' => 'ConstraintViolationList',
                'hydra:title' => 'An error occurred',
                'violations' => [
                    [
                        'propertyPath' => 'name',
                        'message' => 'Cette valeur ne doit pas être vide.',
                    ],
                ],
            ],
        ];

        yield [
            [
                'name' => null,
            ],
            [
                '@context' => '/contexts/ConstraintViolationList',
                '@type' => 'ConstraintViolationList',
                'hydra:title' => 'An error occurred',
                'violations' => [
                    [
                        'propertyPath' => 'name',
                        'message' => 'Cette valeur ne doit pas être vide.',
                    ],
                ],
            ],
        ];

        yield [
            [
                'name' => '',
            ],
            [
                '@context' => '/contexts/ConstraintViolationList',
                '@type' => 'ConstraintViolationList',
                'hydra:title' => 'An error occurred',
                'violations' => [
                    [
                        'propertyPath' => 'name',
                        'message' => 'Cette valeur ne doit pas être vide.',
                    ],
                ],
            ],
        ];

        yield [
            [
                'name' => ByteString::fromRandom(256),
            ],
            [
                '@context' => '/contexts/ConstraintViolationList',
                '@type' => 'ConstraintViolationList',
                'hydra:title' => 'An error occurred',
                'violations' => [
                    [
                        'propertyPath' => 'name',
                        'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 255 caractères.',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideInvalidCases
     */
    public function testInvalidCases(array $payload, array $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $response = $client->request('POST', '/folders', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains($expected);
    }

    public static function provideValidCases(): iterable
    {
        yield [
            [
                'name' => 'My folder 1',
            ],
            [
                'name' => 'My folder 1',
                'type' => 'personal',
                'usersCount' => 0,
            ],
        ];

        yield [
            [
                'name' => 'My folder 2',
            ],
            [
                'name' => 'My folder 2',
                'type' => 'personal',
                'usersCount' => 0,
            ],
        ];
    }

    /**
     * @dataProvider provideValidCases
     */
    public function testValidCases(array $payload, array $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('POST', '/folders', [
            'json' => $payload,
        ]);

        self::assertResponseIsSuccessful(422);
        self::assertJsonContains($expected);
    }
}
