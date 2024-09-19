<?php

namespace App\User\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;
use Symfony\Component\String\ByteString;

class UserPutSkillsAndLanguagesTest extends ApiTestCase
{
    public static function provideWithValidDataCases(): iterable
    {
        return [
            [
                [
                    'skills' => [
                        [
                            'skill' => '/skills/1',
                            'main' => true,
                        ],
                        [
                            'skill' => '/skills/2',
                            'main' => true,
                        ],
                        [
                            'skill' => [
                                'name' => 'New Skill',
                            ],
                            'main' => true,
                        ],
                        [
                            'skill' => [
                                'name' => 'New Super Skill',
                            ],
                            'main' => false,
                        ],
                    ],
                    'languages' => [
                        [
                            'id' => '/user_languages/1',
                            'language' => 'es',
                            'languageLevel' => 'native_or_bilingual',
                        ],
                        [
                            'language' => 'en',
                            'languageLevel' => null,
                        ],
                    ],
                    'softSkills' => [
                        '/soft_skills/1',
                        '/soft_skills/2',
                        '/soft_skills/3',
                    ],
                ],
                [
                    '@context' => '/contexts/User',
                    '@type' => 'User',
                    'skills' => [
                        [
                            '@type' => 'UserSkill',
                            'skill' => [
                                '@type' => 'Skill',
                                'name' => 'php',
                            ],
                            'main' => true,
                        ],
                        [
                            '@type' => 'UserSkill',
                            'skill' => [
                                '@type' => 'Skill',
                                'name' => 'java',
                            ],
                            'main' => true,
                        ],
                        [
                            '@type' => 'UserSkill',
                            'skill' => [
                                '@type' => 'Skill',
                                'name' => 'New Skill',
                            ],
                            'main' => true,
                        ],
                        [
                            '@type' => 'UserSkill',
                            'skill' => [
                                '@type' => 'Skill',
                                'name' => 'New Super Skill',
                            ],
                            'main' => false,
                        ],
                    ],
                    'languages' => [
                        [
                            '@id' => '/user_languages/1',
                            '@type' => 'UserLanguage',
                            'id' => 1,
                            'language' => 'es',
                            'languageLevel' => 'native_or_bilingual',
                        ],
                        [
                            '@type' => 'UserLanguage',
                            'language' => 'en',
                            'languageLevel' => null,
                        ],
                    ],
                    'softSkills' => [
                        [
                            '@type' => 'SoftSkill',
                            'name' => 'SoftSkill 1',
                        ],
                        [
                            '@type' => 'SoftSkill',
                            'name' => 'SoftSkill 2',
                        ],
                        [
                            '@type' => 'SoftSkill',
                            'name' => 'SoftSkill 3',
                        ],
                    ],
                    'formStep' => 'skills_and_languages',
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

        $client->request('PUT', '/users/1/skills_and_languages', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(401);

        $client->request('PUT', '/users/2/skills_and_languages', [
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
        $client->request('PUT', '/users/2/skills_and_languages', [
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
        $client->request('PUT', '/users/1/skills_and_languages', [
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
        $client->request('PUT', '/users/1/skills_and_languages', [
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
                    'skills' => [
                        [
                            '@type' => 'UserSkill',
                            'skill' => [
                                '@type' => 'Skill',
                                'name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum facilisis facilisis justo, nec bibendum nulla euismod id libero.',
                                'slug' => 'lorem-ipsum-dolor-sit-amet-consectetur-adipiscing-elit-vestibulum-facilisis-facilisis-justo-nec-bibendum-nulla-euismod-id-libero',
                            ],
                            'main' => false,
                        ],
                    ],
                    'languages' => [
                        [
                            'id' => '/user_languages/1',
                            'language' => ByteString::fromRandom(256),
                            'languageLevel' => ByteString::fromRandom(256),
                        ],
                    ],
                    'softSkills' => [],
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'skills[0].skill.name',
                            'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 128 caractères.',
                        ],
                        [
                            'propertyPath' => 'skills',
                            'message' => 'Ce champ doit contenir 3 éléments ou plus.',
                        ],
                        [
                            'propertyPath' => 'languages[0].language',
                            'message' => 'Cette valeur doit être l\'un des choix proposés.',
                        ],
                        [
                            'propertyPath' => 'languages[0].languageLevel',
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
        $client->request('PUT', '/users/1/skills_and_languages', [
            'json' => $payload,
        ]);

        self::assertJsonContains($expected);
    }

    public static function provideWithInvalidDataCases(): iterable
    {
        return [
            'free' => [
                [
                    'skills' => [
                        [
                            '@type' => 'UserSkill',
                            'skill' => [
                                '@type' => 'Skill',
                                'name' => 'php',
                                'slug' => 'php',
                            ],
                            'main' => true,
                        ],
                        [
                            '@type' => 'UserSkill',
                            'skill' => [
                                '@type' => 'Skill',
                                'name' => 'java',
                                'slug' => 'java',
                            ],
                            'main' => true,
                        ],
                        [
                            '@type' => 'UserSkill',
                            'skill' => [
                                '@type' => 'Skill',
                                'name' => 'Adobe',
                                'slug' => 'adobe',
                            ],
                            'main' => true,
                        ],
                        [
                            '@type' => 'UserSkill',
                            'skill' => [
                                '@type' => 'Skill',
                                'name' => 'Android',
                                'slug' => 'android',
                            ],
                            'main' => true,
                        ],
                        [
                            '@type' => 'UserSkill',
                            'skill' => [
                                '@type' => 'Skill',
                                'name' => 'HTML',
                                'slug' => 'html',
                            ],
                            'main' => true,
                        ],
                        [
                            '@type' => 'UserSkill',
                            'skill' => [
                                '@type' => 'Skill',
                                'name' => 'Magento',
                                'slug' => 'magento',
                            ],
                            'main' => true,
                        ],
                    ],
                    'softSkills' => [],
                    'languages' => [],
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'skills',
                            'message' => 'Vous pouvez mettre en avant jusqu\'à 5 en les marquant avec une étoile.',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideWithInvalidDataCases
     */
    public function testWithInvalidData(array $payload, array $expected): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient(); // id 1

        // 2 - put Skills on User
        $client->request('PUT', '/users/1/skills_and_languages', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains($expected);
    }
}
