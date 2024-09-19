<?php

namespace App\User\Tests\Functional\Turnover\User;

use App\Tests\Functional\ApiTestCase;

class UserShareTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('POST', '/user_share', [
            'json' => [
                'email' => 'henri.duflot@le-bureau-des-legendes.fr',
                'user' => '/users/42',
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public static function provideWithEmptyOrInvalidDataCases(): iterable
    {
        return [
            [
                [
                    'email' => '',
                    'user' => '/users/42',
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
                    ],
                ],
                [
                    'email' => 'henri.duflot@le-bureau-des-legendes',
                    'user' => '/users/42',
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
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('POST', '/user_share', [
            'json' => $payload,
        ]);

        self::assertJsonContains($expected);

        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testWithValidData(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('POST', '/user_share', [
            'json' => [
                'email' => 'henri.duflot@le-bureau-des-legendes.fr',
                'user' => '/users/42',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains([
            '@context' => '/contexts/UserShare',
            '@type' => 'UserShare',
            'email' => 'henri.duflot@le-bureau-des-legendes.fr',
            'sharedBy' => '/recruiters/1',
        ]);
    }
}
