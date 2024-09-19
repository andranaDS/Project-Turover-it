<?php

namespace App\User\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;
use Symfony\Component\String\ByteString;

class UserPatchForumPreferencesTest extends ApiTestCase
{
    public static function provideWithValidDataCases(): iterable
    {
        return [
            [
                [
                    'jobTitle' => 'Peintre',
                    'website' => 'http://elisabeth.vigee-le-brun.com',
                    'signature' => 'Elisabeth Vigée Le Brun.',
                    'nickname' => 'evigeelebrun',
                    'displayAvatar' => true,
                ],
                [
                    '@context' => '/contexts/User',
                    '@type' => 'User',
                    'jobTitle' => 'Peintre',
                    'website' => 'http://elisabeth.vigee-le-brun.com',
                    'signature' => 'Elisabeth Vigée Le Brun.',
                    'nickname' => 'evigeelebrun',
                    'displayAvatar' => true,
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

        $client->request('PATCH', '/users/1/forum_preferences', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(401);

        $client->request('PATCH', '/users/2/forum_preferences', [
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
        $client->request('PATCH', '/users/2/forum_preferences', [
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
        $client->request('PATCH', '/users/1/forum_preferences', [
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
        $client->request('PATCH', '/users/1/forum_preferences', [
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
                    'jobTitle' => ByteString::fromRandom(256),
                    'website' => ByteString::fromRandom(255),
                    'signature' => ByteString::fromRandom(301),
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
                            'propertyPath' => 'jobTitle',
                            'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 255 caractères.',
                        ],
                        [
                            'propertyPath' => 'website',
                            'message' => 'Cette valeur n\'est pas une URL valide.',
                        ],
                        [
                            'propertyPath' => 'signature',
                            'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 300 caractères.',
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
        $client->request('PATCH', '/users/1/forum_preferences', [
            'json' => $payload,
        ]);

        self::assertJsonContains($expected);
    }
}
