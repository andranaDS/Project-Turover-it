<?php

namespace App\Recruiter\Tests\Functional\Turnover\Recruiter;

use App\Tests\Functional\ApiTestCase;

class PostItemSecondaryTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('POST', '/companies/mine/recruiters', [
            'json' => [
                'email' => 'saul.goodman@breaking-bad.com',
                'firstName' => 'Saul',
                'lastName' => 'Goodman',
                'termsOfService' => true,
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedWithSecondaryAccount(): void
    {
        $client = static::createTurnoverAuthenticatedClient('jesse.pinkman@breaking-bad.com');
        $client->request('POST', '/companies/mine/recruiters', [
            'json' => [
                'email' => 'saul.goodman@breaking-bad.com',
                'firstName' => 'Saul',
                'lastName' => 'Goodman',
                'termsOfService' => true,
            ],
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    public static function provideValidCases(): iterable
    {
        return [
            'valid' => [
                [
                    'email' => 'dustin.henderson@stranger-things.com',
                    'firstName' => 'Dustin',
                    'lastName' => 'Henderson',
                ],
                [
                    '@context' => '/contexts/Recruiter',
                    '@type' => 'Recruiter',
                    'email' => 'dustin.henderson@stranger-things.com',
                    'firstName' => 'Dustin',
                    'lastName' => 'Henderson',
                    'company' => [
                        '@type' => 'Company',
                        'name' => 'Company 1',
                        'slug' => 'company-1',
                        'businessActivity' => '/company_business_activities/1',
                    ],
                    'site' => null,
                    'main' => false,
                ],
            ],
            'valid_with_site' => [
                [
                    'email' => 'dustin.henderson@stranger-things.com',
                    'firstName' => 'Dustin',
                    'lastName' => 'Henderson',
                    'site' => '/sites/1',
                ],
                [
                    '@context' => '/contexts/Recruiter',
                    '@type' => 'Recruiter',
                    'email' => 'dustin.henderson@stranger-things.com',
                    'firstName' => 'Dustin',
                    'lastName' => 'Henderson',
                    'company' => [
                        '@type' => 'Company',
                        'name' => 'Company 1',
                        'slug' => 'company-1',
                        'businessActivity' => '/company_business_activities/1',
                    ],
                    'site' => [
                        '@type' => 'Site',
                        'id' => 1,
                        'name' => 'Site 1 - Company 1',
                        'slug' => 'site-1-company-1',
                    ],
                    'main' => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideValidCases
     */
    public function testValidCases(array $payload, array $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('POST', '/companies/mine/recruiters', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(201);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains($expected);

        // check registration email
        $email = self::getMailerMessage();
        self::assertNotNull($email);
        self::assertEmailHeaderSame($email, 'from', 'Turnover-IT <service_clients@turnover-it.com>');
        self::assertEmailHeaderSame($email, 'to', $payload['email']);
        self::assertEmailHeaderSame($email, 'subject', 'TEST: Bienvenue sur Turnover-IT !');
        self::assertEmailTextBodyContains($email, 'Walter White vous invite à rejoindre Turnover-IT !');
        self::assertEmailTextBodyContains($email, 'Votre mot de passe :');
        self::assertEmailTextBodyContains($email, 'ME CONNECTER');
        self::assertEmailTextBodyContains($email, 'https://front.turnover-it.localhost/login');
    }

    public static function provideInvalidCases(): iterable
    {
        return [
            'empty' => [
                [
                    'email' => '',
                    'firstName' => '',
                    'lastName' => '',
                    'termsOfService' => false,
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
                            'propertyPath' => 'username',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'firstName',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'lastName',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                    ],
                ],
            ],
            'email_invalid' => [
                [
                    'email' => 'dustin.henderson',
                    'firstName' => 'Dustin',
                    'lastName' => 'Henderson',
                    'termsOfService' => true,
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'email',
                            'message' => "Cette valeur n'est pas une adresse email valide.",
                        ],
                    ],
                ],
            ],
            'email_already_used' => [
                [
                    'email' => 'walter.white@breaking-bad.com',
                    'firstName' => 'Dustin',
                    'lastName' => 'Henderson',
                    'termsOfService' => true,
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
        $client->request('POST', '/companies/mine/recruiters', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains($expected);
    }
}
