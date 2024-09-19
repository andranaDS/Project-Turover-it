<?php

namespace App\Company\Tests\Functional\Turnover\Site;

use App\Tests\Functional\ApiTestCase;
use Symfony\Component\String\ByteString;

class SitePostTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('POST', '/sites', ['json' => []]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedWithSecondaryAccount(): void
    {
        $client = static::createTurnoverAuthenticatedClient('jesse.pinkman@breaking-bad.com');
        $client->request('POST', '/sites', ['json' => []]);

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsAuthorizedUser(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('POST', '/sites', ['json' => []]);

        self::assertResponseStatusCodeSame(422);
    }

    public static function provideInvalidDataAsAuthorizedUserCases(): iterable
    {
        return [
            'blank' => [
                [
                    'json' => [
                        'name' => '',
                        'ip' => '',
                    ],
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'name',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'ip',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                    ],
                ],
            ],
            'invalid' => [
                [
                    'json' => [
                        'name' => ByteString::fromRandom(256),
                        'ip' => '127.0.0.1',
                    ],
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'name',
                            'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 255 caractères.',
                        ],
                        [
                            'propertyPath' => 'ip',
                            'message' => 'Cette adresse IP n\'est pas valide.',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideInvalidDataAsAuthorizedUserCases
     */
    public function testInvalidDataAsAuthorizedUser(array $payload, array $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('POST', '/sites', $payload);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains($expected);
    }

    public function testWithValidData(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('POST', '/sites', [
            'json' => [
                'name' => 'Site 3  - Company 1',
                'ip' => '1.1.1.3',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Site',
            '@type' => 'Site',
            'name' => 'Site 3  - Company 1',
            'slug' => 'site-3-company-1',
            'ip' => '1.1.1.3',
        ]);
    }
}
