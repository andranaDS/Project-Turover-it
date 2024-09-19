<?php

namespace App\User\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;

class UsersPostTest extends ApiTestCase
{
    public function testAlreadyLogged(): void
    {
        $client = static::createFreeWorkAuthenticatedClient();

        $client->request('POST', '/users', [
            'json' => [
                'email' => 'zinedine.zidane@free-work.fr',
                'plainPassword' => '1P@ssw0rd1',
                'termsOfService' => true,
                'notification' => [
                    'marketingNewsletter' => true,
                ],
            ],
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    public static function provideWithValidDataCases(): iterable
    {
        return [
            [
                [
                    'email' => 'zinedine.zidane@free-work.fr',
                    'plainPassword' => '1P@ssw0rd1',
                    'termsOfService' => true,
                    'notification' => [
                        'marketingNewsletter' => true,
                    ],
                ],
                [
                    '@context' => '/contexts/User',
                    '@type' => 'User',
                    'email' => 'zinedine.zidane@free-work.fr',
                    'notification' => [
                        'marketingNewsletter' => true,
                    ],
                    'roles' => ['ROLE_USER'],
                    'firstName' => null,
                    'lastName' => null,
                    'termsOfService' => true,
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideWithValidDataCases
     */
    public function testWithValidData(array $payload, array $expected): void
    {
        $client = static::createFreeWorkClient();

        $client->request('POST', '/users', [
            'json' => $payload,
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains($expected);

        self::assertEmailCount(1);

        $email = self::getMailerMessage();
        self::assertNotNull($email);
        self::assertEmailHeaderSame($email, 'from', 'Free-Work <account@free-work.com>');
        self::assertEmailHeaderSame($email, 'to', 'zinedine.zidane@free-work.fr');
        self::assertEmailHeaderSame($email, 'subject', 'TEST: Veuillez confirmer votre e-mail');
        self::assertEmailTextBodyContains($email, 'Veuillez la confirmer en cliquant sur le lien suivant, afin d’activer votre compte 👇');
        self::assertEmailTextBodyContains($email, 'Confirmer votre e-mail');
    }

    public static function provideWithValidAndUselessDataCases(): iterable
    {
        return [
            [
                [
                    'email' => 'didier.deschamps@free-work.fr',
                    'nickname' => 'Ladeche',
                    'plainPassword' => '1P@ssw0rd1',
                    'firstName' => 'Didier',
                    'lastName' => 'Deschamps',
                    'termsOfService' => true,
                    'notification' => [
                        'marketingNewsletter' => false,
                    ],
                ],
                [
                    '@context' => '/contexts/User',
                    '@type' => 'User',
                    'email' => 'didier.deschamps@free-work.fr',
                    'roles' => ['ROLE_USER'],
                    'firstName' => null,
                    'lastName' => null,
                    'termsOfService' => true,
                    'notification' => [
                        'marketingNewsletter' => false,
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideWithValidAndUselessDataCases
     */
    public function testWithValidAndUselessData(array $payload, array $expected): void
    {
        $client = static::createFreeWorkClient();

        $response = $client->request('POST', '/users', [
            'json' => $payload,
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains($expected);
        $userId = $response->toArray()['id'];

        self::assertJsonContains([
            'nickname' => 'Free-Worker-' . $userId,
            'nicknameSlug' => 'free-worker-' . $userId,
        ]);

        self::assertEmailCount(1);
    }

    public static function provideWithEmptyOrInvalidDataCases(): iterable
    {
        return [
            [
                [
                    'email' => '',
                    'plainPassword' => '',
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'email',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'email',
                            'message' => 'Cette valeur n\'est pas une adresse email valide.',
                        ],
                        [
                            'propertyPath' => 'plainPassword',
                            'message' => 'La force du mot de passe doit être au minimum "Bon".',
                        ],
                        [
                            'propertyPath' => 'termsOfService',
                            'message' => 'Les conditions générales d\'utilisations doivent être acceptés.',
                        ],
                    ],
                ],
            ],
            [
                [
                    'email' => 'john',
                    'plainPassword' => 'P@ssw0rd',
                    'termsOfService' => false,
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'email',
                            'message' => 'Cette valeur n\'est pas une adresse email valide.',
                        ],
                        [
                            'propertyPath' => 'plainPassword',
                            'message' => 'La force du mot de passe doit être au minimum "Bon".',
                        ],
                        [
                            'propertyPath' => 'termsOfService',
                            'message' => 'Les conditions générales d\'utilisations doivent être acceptés.',
                        ],
                    ],
                ],
            ],
            [
                [
                    'email' => 'marcel.desailly@free-work.fr',
                    'plainPassword' => 'P@ssw0rd',
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'plainPassword',
                            'message' => 'La force du mot de passe doit être au minimum "Bon".',
                        ],
                        [
                            'propertyPath' => 'termsOfService',
                            'message' => 'Les conditions générales d\'utilisations doivent être acceptés.',
                        ],
                    ],
                ],
            ],
            [
                [
                    'email' => 'user@free-work.fr',
                    'plainPassword' => '1P@ssw0rd1',
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'email',
                            'message' => 'Cette valeur est déjà utilisée.',
                        ],
                        [
                            'propertyPath' => 'termsOfService',
                            'message' => 'Les conditions générales d\'utilisations doivent être acceptés.',
                        ],
                    ],
                ],
            ],
            [
                [
                    'email' => 'use..r@free-work.fr',
                    'plainPassword' => '1P@ssw0rd1',
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'email',
                            'message' => 'Cette valeur n\'est pas une adresse email valide.',
                        ],
                        [
                            'propertyPath' => 'termsOfService',
                            'message' => 'Les conditions générales d\'utilisations doivent être acceptés.',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideWithEmptyOrInvalidDataCases
     */
    public function testWithEmptyOrInvalidData(array $payload, array $expected): void
    {
        $client = static::createFreeWorkClient();

        $client->request('POST', '/users', [
            'json' => $payload,
        ]);

        self::assertJsonContains($expected);

        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}
