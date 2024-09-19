<?php

namespace App\User\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;
use App\User\Enum\UserProfileStep;
use Symfony\Component\String\ByteString;

class UserPatchAboutMeTest extends ApiTestCase
{
    public static function provideWithValidDataCases(): iterable
    {
        return [
            [
                [
                    'introduceYourself' => 'John Doe',
                    'profileWebsite' => 'http://john-doe.com',
                    'profileLinkedInProfile' => 'http://john-doe.com/linkedin',
                    'profileProjectWebsite' => 'http://john-doe.com/projects',
                ],
                [
                    '@context' => '/contexts/User',
                    'introduceYourself' => 'John Doe',
                    'profileWebsite' => 'http://john-doe.com',
                    'profileLinkedInProfile' => 'http://john-doe.com/linkedin',
                    'profileProjectWebsite' => 'http://john-doe.com/projects',
                    'formStep' => 'about_me',
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

        $client->request('PATCH', '/users/1/about_me', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(401);

        $client->request('PATCH', '/users/2/about_me', [
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
        $client->request('PATCH', '/users/2/about_me', [
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
        $client->request('PATCH', '/users/1/about_me', [
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
        $client->request('PATCH', '/users/1/about_me', [
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
                    'introduceYourself' => ByteString::fromRandom(601),
                    'profileWebsite' => ByteString::fromRandom(255),
                    'profileLinkedInProfile' => ByteString::fromRandom(255),
                    'profileProjectWebsite' => ByteString::fromRandom(255),
                    'formStep' => UserProfileStep::ABOUT_ME,
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'profileWebsite',
                            'message' => 'Cette valeur n\'est pas une URL valide.',
                        ],
                        [
                            'propertyPath' => 'profileLinkedInProfile',
                            'message' => 'Cette valeur n\'est pas une URL valide.',
                        ],
                        [
                            'propertyPath' => 'profileProjectWebsite',
                            'message' => 'Cette valeur n\'est pas une URL valide.',
                        ],
                        [
                            'propertyPath' => 'introduceYourself',
                            'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 600 caractères.',
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
        $client->request('PATCH', '/users/1/about_me', [
            'json' => $payload,
        ]);

        self::assertJsonContains($expected);
    }
}
