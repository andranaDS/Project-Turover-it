<?php

namespace App\Folder\Tests\Functional\Folder;

use App\Tests\Functional\ApiTestCase;
use Symfony\Component\String\ByteString;

class FolderPutTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('PUT', '/folders/1', [
            'json' => ['name' => 'Personal folder'],
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsNotOwner(): void
    {
        $client = static::createTurnoverAuthenticatedClient('eddard.stark@got.com');
        $client->request('PUT', '/folders/1', [
            'json' => ['name' => 'Personal folder'],
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    public static function provideLoggedAsOwnerWithInvalidCases(): iterable
    {
        yield [
            'path' => '/folders/1',
            'payload' => [
                'name' => 'My folder 1 updated',
            ],
            'expected' => [
                '@context' => '/contexts/ConstraintViolationList',
                '@type' => 'ConstraintViolationList',
                'hydra:title' => 'An error occurred',
                'violations' => [
                    [
                        'propertyPath' => 'type',
                        'message' => 'Cette valeur doit être égale à "personal".',
                    ],
                ],
            ],
        ];

        yield [
            'path' => '/folders/120',
            'payload' => [
                'name' => null,
            ],
            'expected' => [
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
            'path' => '/folders/120',
            'payload' => [
                'name' => '',
            ],
            'expected' => [
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
            'path' => '/folders/120',
            'payload' => [
                'name' => ByteString::fromRandom(256),
            ],
            'expected' => [
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
     * @dataProvider provideLoggedAsOwnerWithInvalidCases
     */
    public function testLoggedAsOwnerWithInvalidCases(string $path, array $payload, array $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('PUT', $path, [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains($expected);
    }

    public static function provideLoggedAsOwnerWithValidCases(): iterable
    {
        yield [
            'recruiter' => 'walter.white@breaking-bad.com',
            'path' => '/folders/120',
            'payload' => [
                'name' => 'My folder 1 updated',
            ],
            'expected' => [
                'name' => 'My folder 1 updated',
                'type' => 'personal',
                'usersCount' => 0,
            ],
        ];

        yield [
            'recruiter' => 'arya.stark@got.com',
            'path' => '/folders/122',
            'payload' => [
                'name' => 'My folder 2 updated',
            ],
            'expected' => [
                'name' => 'My folder 2 updated',
                'type' => 'personal',
                'usersCount' => 1,
            ],
        ];
    }

    /**
     * @dataProvider provideLoggedAsOwnerWithValidCases
     */
    public function testLoggedAsOwnerWithValidCases(string $recruiter, string $path, array $payload, array $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient($recruiter);
        $client->request('PUT', $path, [
            'json' => $payload,
        ]);

        self::assertResponseIsSuccessful(422);
        self::assertJsonContains($expected);
    }
}
