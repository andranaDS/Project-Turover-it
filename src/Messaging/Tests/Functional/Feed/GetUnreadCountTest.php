<?php

namespace App\Messaging\Tests\Functional\Feed;

use App\Tests\Functional\ApiTestCase;
use Nette\Utils\Json;

class GetUnreadCountTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();

        $client->request('GET', '/feeds/unread/count');
        self::assertResponseStatusCodeSame(401);
    }

    public static function provideLoggedCases(): iterable
    {
        yield ['claude.monet@free-work.fr', 1];
        yield ['vincent.van-gogh@free-work.fr', 0];
    }

    /**
     * @dataProvider provideLoggedCases
     */
    public function testLogged(string $email, int $unreadCount): void
    {
        $client = static::createFreeWorkAuthenticatedClient($email);
        $response = $client->request('GET', '/feeds/unread/count');

        self::assertSame($unreadCount, Json::decode($response->getContent()));

        self::assertResponseIsSuccessful();
    }
}
