<?php

namespace App\Notification\Tests\Functional\Turnover\Notification;

use App\Tests\Functional\ApiTestCase;

class NotificationsReadTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('POST', '/notifications/read');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLogged(): void
    {
        $client = static::createTurnoverAuthenticatedClient();

        // 0 - count before
        $response = $client->request('GET', '/notifications/unread/count');
        self::assertResponseIsSuccessful();
        self::assertSame(2, (int) $response->getContent());

        // 1 - mark all notifications as read
        $client->request('POST', '/notifications/read');
        self::assertResponseIsSuccessful();

        // 2 - count after
        $response = $client->request('GET', '/notifications/unread/count');
        self::assertResponseIsSuccessful();
        self::assertSame(0, (int) $response->getContent());
    }
}
