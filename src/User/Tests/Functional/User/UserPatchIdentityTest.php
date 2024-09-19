<?php

namespace App\User\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;
use Symfony\Component\String\ByteString;

class UserPatchIdentityTest extends ApiTestCase
{
    public static function provideWithValidDataCases(): iterable
    {
        return [
            [
                [
                    'firstName' => 'Elisabeth',
                    'lastName' => 'Vigée Le Brun',
                    'gender' => 'female',
                    'nickname' => 'evigeelebrun',
                ],
                [
                    '@context' => '/contexts/User',
                    '@type' => 'User',
                    'firstName' => 'Elisabeth',
                    'lastName' => 'Vigée Le Brun',
                    'gender' => 'female',
                    'nickname' => 'evigeelebrun',
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideWithValidDataCases
     */
    public function testNotLogged(array $payload): void
    {
        $client = static::createFreeWorkClient();

        $client->request('PATCH', '/users/1/identity', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(401);

        $client->request('PATCH', '/users/2/identity', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    /**
     * @dataProvider provideWithValidDataCases
     */
    public function testLoggedAsUser(array $payload): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('PATCH', '/users/2/identity', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    /**
     * @dataProvider provideWithValidDataCases
     */
    public function testLoggedAsAdmin(array $payload): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('PATCH', '/users/1/identity', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    /**
     * @dataProvider provideWithValidDataCases
     */
    public function testWithValidDataOnItsOwnEntityAndLoggedAsUser(array $payload, array $expected): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient(); // id 1
        $client->request('PATCH', '/users/1/identity', [
            'json' => $payload,
        ]);

        self::assertResponseIsSuccessful();
        self::assertJsonContains($expected);
    }

    public static function provideWithErrorOnItsOwnEntityAndLoggedAsUserCases(): iterable
    {
        return [
            [
                [
                    'firstName' => ByteString::fromRandom(256),
                    'lastName' => ByteString::fromRandom(256),
                    'gender' => ByteString::fromRandom(10),
                    'nickname' => ByteString::fromRandom(256),
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'nickname',
                            'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 30 caractères.',
                        ],
                        [
                            'propertyPath' => 'firstName',
                            'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 255 caractères.',
                        ],
                        [
                            'propertyPath' => 'lastName',
                            'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 255 caractères.',
                        ],
                        [
                            'propertyPath' => 'gender',
                            'message' => 'Cette valeur doit être l\'un des choix proposés.',
                        ],
                    ],
                ],
            ],
            [
                [
                    'firstName' => '',
                    'lastName' => '',
                    'gender' => '',
                    'nickname' => '',
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'nickname',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'firstName',
                            'message' => 'Cette chaîne est trop courte. Elle doit avoir au minimum 2 caractères.',
                        ],
                        [
                            'propertyPath' => 'firstName',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'lastName',
                            'message' => 'Cette chaîne est trop courte. Elle doit avoir au minimum 2 caractères.',
                        ],
                        [
                            'propertyPath' => 'lastName',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'gender',
                            'message' => 'Cette valeur doit être l\'un des choix proposés.',
                        ],
                    ],
                ],
            ],
            [
                [
                    'firstName' => ByteString::fromRandom(1),
                    'lastName' => ByteString::fromRandom(1),
                    'gender' => '',
                    'nickname' => '',
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'nickname',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'firstName',
                            'message' => 'Cette chaîne est trop courte. Elle doit avoir au minimum 2 caractères.',
                        ],
                        [
                            'propertyPath' => 'lastName',
                            'message' => 'Cette chaîne est trop courte. Elle doit avoir au minimum 2 caractères.',
                        ],
                        [
                            'propertyPath' => 'gender',
                            'message' => 'Cette valeur doit être l\'un des choix proposés.',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideWithErrorOnItsOwnEntityAndLoggedAsUserCases
     */
    public function testWithErrorOnItsOwnEntityAndLoggedAsUser(array $payload, array $expected): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient(); // id 1
        $client->request('PATCH', '/users/1/identity', [
            'json' => $payload,
        ]);

        self::assertJsonContains($expected);
    }
}
