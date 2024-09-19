<?php

namespace App\Messaging\Tests\Functional\Message;

use App\Tests\Functional\ApiTestCase;

class MessagesGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();

        $client->request('GET', '/messages');
        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();

        $client->request('GET', '/messages');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();

        $client->request('GET', '/messages');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testUserSeeingOnlyHisMessages(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/messages');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains([
            'hydra:member' => [
                [
                    'author' => '/users/6',
                ],
                [
                    'author' => '/users/6',
                ],
            ],
            'hydra:totalItems' => 4,
        ]);

        $client = static::createFreeWorkAuthenticatedClient('vincent.van-gogh@free-work.fr');
        $client->request('GET', '/messages');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains([
            'hydra:member' => [
                [
                    'author' => '/users/7',
                ],
            ],
            'hydra:totalItems' => 1,
        ]);
    }
}
