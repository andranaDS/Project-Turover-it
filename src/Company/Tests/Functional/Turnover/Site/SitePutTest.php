<?php

namespace App\Company\Tests\Functional\Turnover\Site;

use App\Tests\Functional\ApiTestCase;
use Symfony\Component\String\ByteString;

class SitePutTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('PUT', '/sites/1');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedWithoutMainAccount(): void
    {
        $client = static::createTurnoverAuthenticatedClient('jesse.pinkman@breaking-bad.com');
        $client->request('PUT', '/sites/1', ['json' => []]);

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsAuthorizeUser(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('PUT', '/sites/1', ['json' => []]);

        self::assertResponseStatusCodeSame(200);
    }

    public static function provideInvalidDataAsAuthorizeUserCases(): iterable
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
     * @dataProvider provideInvalidDataAsAuthorizeUserCases
     */
    public function testInvalidDataAsAuthorizeUser(array $payload, array $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('PUT', '/sites/1', $payload);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains($expected);
    }

    public function testWithValidData(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('PUT', '/sites/1', [
            'json' => [
                'name' => 'Site 1 edited - Company 1',
                'ip' => '1.1.1.3',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Site',
            '@type' => 'Site',
            'name' => 'Site 1 edited - Company 1',
            'slug' => 'site-1-edited-company-1',
            'ip' => '1.1.1.3',
        ]);
    }
}
