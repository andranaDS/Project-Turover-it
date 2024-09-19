<?php

namespace App\Recruiter\Tests\Functional\Turnover\Recruiter;

use App\Tests\Functional\ApiTestCase;

class PatchChangePasswordTest extends ApiTestCase
{
    public function testNotLoggedCases(): void
    {
        $client = static::createTurnoverClient();
        $client->request('PATCH', '/recruiters/2/change_password', [
            'json' => [
                'oldPassword' => 'P@ssw0rd',
                'newPassword' => 'NewP@ssword',
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedOnOtherRecruiter(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('PATCH', '/recruiters/2/change_password', [
            'json' => [
                'oldPassword' => 'P@ssw0rd',
                'newPassword' => 'NewP@ssword',
            ],
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    public static function provideValidCases(): iterable
    {
        yield [
            'email' => 'jesse.pinkman@breaking-bad.com',
            'path' => '/recruiters/me',
            'payload' => [
                'oldPassword' => 'P@ssw0rd',
                'newPassword' => 'N3w!P@ssword',
            ],
            'expectedGet' => [
                '@context' => '/contexts/Recruiter',
                '@id' => '/recruiters/2',
                '@type' => 'Recruiter',
                'id' => 2,
                'email' => 'jesse.pinkman@breaking-bad.com',
                'passwordUpdateRequired' => false,
            ],
            'expectedPatch' => [
                '@context' => '/contexts/Recruiter',
                '@id' => '/recruiters/2',
                '@type' => 'Recruiter',
                'id' => 2,
                'email' => 'jesse.pinkman@breaking-bad.com',
                'passwordUpdateRequired' => false,
            ],
        ];

        yield [
            'email' => 'jesse.pinkman@breaking-bad.com',
            'path' => '/recruiters/2',
            'payload' => [
                'oldPassword' => 'P@ssw0rd',
                'newPassword' => 'N3w!P@ssword',
            ],
            'expectedGet' => [
                '@context' => '/contexts/Recruiter',
                '@id' => '/recruiters/2',
                '@type' => 'Recruiter',
                'id' => 2,
                'email' => 'jesse.pinkman@breaking-bad.com',
                'passwordUpdateRequired' => false,
            ],
            'expectedPatch' => [
                '@context' => '/contexts/Recruiter',
                '@id' => '/recruiters/2',
                '@type' => 'Recruiter',
                'id' => 2,
                'email' => 'jesse.pinkman@breaking-bad.com',
                'passwordUpdateRequired' => false,
            ],
        ];

        yield [
            'email' => 'walter.white@breaking-bad.com',
            'path' => '/recruiters/me',
            'payload' => [
                'newPassword' => 'N3w!P@ssword',
            ],
            'expectedGet' => [
                '@context' => '/contexts/Recruiter',
                '@id' => '/recruiters/1',
                '@type' => 'Recruiter',
                'id' => 1,
                'email' => 'walter.white@breaking-bad.com',
                'passwordUpdateRequired' => true,
            ],
            'expectedPatch' => [
                '@context' => '/contexts/Recruiter',
                '@id' => '/recruiters/1',
                '@type' => 'Recruiter',
                'id' => 1,
                'email' => 'walter.white@breaking-bad.com',
                'passwordUpdateRequired' => false,
            ],
        ];

        yield [
            'email' => 'walter.white@breaking-bad.com',
            'path' => '/recruiters/me',
            'payload' => [
                'oldPassword' => 'P@ssw0rd',
                'newPassword' => 'N3w!P@ssword',
            ],
            'expectedGet' => [
                '@context' => '/contexts/Recruiter',
                '@id' => '/recruiters/1',
                '@type' => 'Recruiter',
                'id' => 1,
                'email' => 'walter.white@breaking-bad.com',
                'passwordUpdateRequired' => true,
            ],
            'expectedPatch' => [
                '@context' => '/contexts/Recruiter',
                '@id' => '/recruiters/1',
                '@type' => 'Recruiter',
                'id' => 1,
                'email' => 'walter.white@breaking-bad.com',
                'passwordUpdateRequired' => false,
            ],
        ];

        yield [
            'email' => 'walter.white@breaking-bad.com',
            'path' => '/recruiters/1',
            'payload' => [
                'newPassword' => 'N3w!P@ssword',
            ],
            'expectedGet' => [
                '@context' => '/contexts/Recruiter',
                '@id' => '/recruiters/1',
                '@type' => 'Recruiter',
                'id' => 1,
                'email' => 'walter.white@breaking-bad.com',
                'passwordUpdateRequired' => true,
            ],
            'expectedPatch' => [
                '@context' => '/contexts/Recruiter',
                '@id' => '/recruiters/1',
                '@type' => 'Recruiter',
                'id' => 1,
                'email' => 'walter.white@breaking-bad.com',
                'passwordUpdateRequired' => false,
            ],
        ];
    }

    /**
     * @dataProvider provideValidCases
     */
    public function testValidCases(string $email, string $path, array $payload, array $expectedGet, array $expectedPatch): void
    {
        $client = static::createTurnoverAuthenticatedClient($email);

        // check passwordUpdateRequired
        $client->request('GET', $path);
        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains($expectedGet);

        // change password
        $client->request('PATCH', "$path/change_password", [
            'json' => $payload,
        ]);

        // check passwordUpdateRequired
        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains($expectedPatch);

        // check login
        $client = static::createTurnoverClient();
        $client->request('POST', '/login', [
            'json' => [
                'email' => $email,
                'password' => $payload['newPassword'],
            ],
        ]);

        self::assertResponseStatusCodeSame(204);
    }

    public static function provideInvalidCases(): iterable
    {
        yield [
            'email' => 'walter.white@breaking-bad.com',
            'payload' => [
                'oldPassword' => '',
                'newPassword' => '',
            ],
            'expected' => [
                '@context' => '/contexts/ConstraintViolationList',
                '@type' => 'ConstraintViolationList',
                'hydra:title' => 'An error occurred',
                'hydra:description' => "oldPassword: Le mot de passe actuel est incorrect.\nnewPassword: La force du mot de passe doit être au minimum \"Bon\".",
                'violations' => [
                    [
                        'propertyPath' => 'oldPassword',
                        'message' => 'Le mot de passe actuel est incorrect.',
                        'code' => null,
                    ],
                    [
                        'propertyPath' => 'newPassword',
                        'message' => 'La force du mot de passe doit être au minimum "Bon".',
                        'code' => null,
                    ],
                ],
            ],
        ];

        yield [
            'email' => 'walter.white@breaking-bad.com',
            'payload' => [
                'newPassword' => '',
            ],
            'expected' => [
                '@context' => '/contexts/ConstraintViolationList',
                '@type' => 'ConstraintViolationList',
                'hydra:title' => 'An error occurred',
                'hydra:description' => 'newPassword: La force du mot de passe doit être au minimum "Bon".',
                'violations' => [
                    [
                        'propertyPath' => 'newPassword',
                        'message' => 'La force du mot de passe doit être au minimum "Bon".',
                        'code' => null,
                    ],
                ],
            ],
        ];

        yield [
            'email' => 'jesse.pinkman@breaking-bad.com',
            'payload' => [
                'newPassword' => '',
            ],
            'expected' => [
                '@context' => '/contexts/ConstraintViolationList',
                '@type' => 'ConstraintViolationList',
                'hydra:title' => 'An error occurred',
                'hydra:description' => "oldPassword: Le mot de passe actuel est incorrect.\nnewPassword: La force du mot de passe doit être au minimum \"Bon\".",
                'violations' => [
                    [
                        'propertyPath' => 'oldPassword',
                        'message' => 'Le mot de passe actuel est incorrect.',
                        'code' => null,
                    ],
                    [
                        'propertyPath' => 'newPassword',
                        'message' => 'La force du mot de passe doit être au minimum "Bon".',
                        'code' => null,
                    ],
                ],
            ],
        ];

        yield [
            'email' => 'walter.white@breaking-bad.com',
            'payload' => [
                'oldPassword' => 'WrongP@ssw0rd',
                'newPassword' => 'N3w!P@ssword',
            ],
            'expected' => [
                '@context' => '/contexts/ConstraintViolationList',
                '@type' => 'ConstraintViolationList',
                'hydra:title' => 'An error occurred',
                'hydra:description' => 'oldPassword: Le mot de passe actuel est incorrect.',
                'violations' => [
                    [
                        'propertyPath' => 'oldPassword',
                        'message' => 'Le mot de passe actuel est incorrect.',
                        'code' => null,
                    ],
                ],
            ],
        ];

        yield [
            'email' => 'walter.white@breaking-bad.com',
            'payload' => [
                'oldPassword' => 'P@ssw0rd',
                'newPassword' => 'SimplePassword',
            ],
            'expected' => [
                '@context' => '/contexts/ConstraintViolationList',
                '@type' => 'ConstraintViolationList',
                'hydra:title' => 'An error occurred',
                'hydra:description' => 'newPassword: La force du mot de passe doit être au minimum "Bon".',
                'violations' => [
                    [
                        'propertyPath' => 'newPassword',
                        'message' => 'La force du mot de passe doit être au minimum "Bon".',
                        'code' => null,
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideInvalidCases
     */
    public function testInvalidCases(string $email, array $payload, array $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient($email);
        $client->request('PATCH', '/recruiters/me/change_password', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains($expected);
    }
}
