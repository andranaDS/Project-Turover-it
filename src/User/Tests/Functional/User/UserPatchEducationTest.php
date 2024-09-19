<?php

namespace App\User\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;
use Symfony\Component\String\ByteString;

class UserPatchEducationTest extends ApiTestCase
{
    public static function provideWithValidDataCases(): iterable
    {
        return [
            'self_taught' => [
                [
                    'formation' => [
                        'diplomaTitle' => '',
                        'diplomaLevel' => 0,
                        'school' => '',
                        'diplomaYear' => 0,
                        'beingObtained' => false,
                        'selfTaught' => true,
                    ],
                ],
                [
                    '@context' => '/contexts/User',
                    '@type' => 'User',
                    'formation' => [
                        '@type' => 'UserFormation',
                        'diplomaTitle' => '',
                        'diplomaLevel' => 0,
                        'school' => '',
                        'diplomaYear' => 0,
                        'beingObtained' => false,
                        'selfTaught' => true,
                    ],
                    'formStep' => 'education',
                ],
            ],
            'being_obtained' => [
                [
                    'formation' => [
                        'diplomaTitle' => 'Diploma Title',
                        'diplomaLevel' => 5,
                        'school' => 'Diploma School',
                        'diplomaYear' => 0,
                        'beingObtained' => true,
                        'selfTaught' => false,
                    ],
                ],
                [
                    '@context' => '/contexts/User',
                    '@type' => 'User',
                    'formation' => [
                        '@type' => 'UserFormation',
                        'diplomaTitle' => 'Diploma Title',
                        'diplomaLevel' => 5,
                        'school' => 'Diploma School',
                        'diplomaYear' => 0,
                        'beingObtained' => true,
                        'selfTaught' => false,
                    ],
                    'formStep' => 'education',
                ],
            ],
            'being_obtained_level_0' => [
                [
                    'formation' => [
                        'diplomaTitle' => 'Diploma Level 0',
                        'diplomaLevel' => 0,
                        'school' => 'Diploma School Level 0',
                        'diplomaYear' => 0,
                        'beingObtained' => true,
                        'selfTaught' => false,
                    ],
                ],
                [
                    '@context' => '/contexts/User',
                    '@type' => 'User',
                    'formation' => [
                        '@type' => 'UserFormation',
                        'diplomaTitle' => 'Diploma Level 0',
                        'diplomaLevel' => 0,
                        'school' => 'Diploma School Level 0',
                        'diplomaYear' => 0,
                        'beingObtained' => true,
                        'selfTaught' => false,
                    ],
                    'formStep' => 'education',
                ],
            ],
            'not_being_obtained_nor_self_taught' => [
                [
                    'formation' => [
                        'diplomaTitle' => 'Diploma Title',
                        'diplomaLevel' => 5,
                        'school' => 'Diploma School',
                        'diplomaYear' => 2020,
                        'beingObtained' => false,
                        'selfTaught' => false,
                    ],
                ],
                [
                    '@context' => '/contexts/User',
                    '@type' => 'User',
                    'formation' => [
                        '@type' => 'UserFormation',
                        'diplomaTitle' => 'Diploma Title',
                        'diplomaLevel' => 5,
                        'school' => 'Diploma School',
                        'diplomaYear' => 2020,
                        'beingObtained' => false,
                        'selfTaught' => false,
                    ],
                    'formStep' => 'education',
                ],
            ],
            [
                'empty' => [
                    'formation' => [
                        'diplomaTitle' => '',
                        'diplomaLevel' => null,
                        'school' => '',
                        'diplomaYear' => 0,
                        'beingObtained' => false,
                        'selfTaught' => false,
                    ],
                ],
                [
                    '@context' => '/contexts/User',
                    '@type' => 'User',
                    'formation' => [
                        '@type' => 'UserFormation',
                        'diplomaTitle' => '',
                        'diplomaLevel' => null,
                        'school' => '',
                        'diplomaYear' => 0,
                        'beingObtained' => false,
                        'selfTaught' => false,
                    ],
                    'formStep' => 'education',
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

        $client->request('PATCH', '/users/1/education', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(401);

        $client->request('PATCH', '/users/2/education', [
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
        $client->request('PATCH', '/users/2/education', [
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
        $client->request('PATCH', '/users/1/education', [
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
        $client->request('PATCH', '/users/1/education', [
            'json' => $payload,
        ]);

        self::assertResponseIsSuccessful();
        self::assertJsonContains($expected);
    }

    public static function provideWithErrorOnItsOwnEntityAndLoggedAsUserCases(): iterable
    {
        return [
            [
                'being_obtained' => [
                    'formation' => [
                        'diplomaTitle' => ByteString::fromRandom(256),
                        'diplomaLevel' => 50000,
                        'school' => ByteString::fromRandom(256),
                        'diplomaYear' => 200000,
                        'beingObtained' => true,
                        'selfTaught' => false,
                    ],
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'formation.diplomaTitle',
                            'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 255 caractères.',
                        ],
                        [
                            'propertyPath' => 'formation.diplomaLevel',
                            'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 2 caractères.',
                        ],
                        [
                            'propertyPath' => 'formation.school',
                            'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 255 caractères.',
                        ],
                        [
                            'propertyPath' => 'formation.diplomaYear',
                            'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 4 caractères.',
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
        $client->request('PATCH', '/users/1/education', [
            'json' => $payload,
        ]);

        self::assertJsonContains($expected);
    }
}
