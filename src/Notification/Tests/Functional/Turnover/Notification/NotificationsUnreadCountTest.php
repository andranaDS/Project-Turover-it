<?php

namespace App\Notification\Tests\Functional\Turnover\Notification;

use App\Tests\Functional\ApiTestCase;

class NotificationsUnreadCountTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/notifications/unread/count');

        self::assertResponseStatusCodeSame(401);
    }

    public static function provideLoggedCases(): iterable
    {
        yield [
            'recruiter' => 'walter.white@breaking-bad.com',
            'expected' => 2,
        ];

        yield [
            'recruiter' => 'jesse.pinkman@breaking-bad.com',
            'expected' => 1,
        ];

        yield [
            'recruiter' => 'gustavo.fring@breaking-bad.com',
            'expected' => 0,
        ];
    }

    /**
     * @dataProvider provideLoggedCases
     */
    public function testLogged(string $recruiter, int $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient($recruiter);
        $response = $client->request('GET', '/notifications/unread/count');

        self::assertResponseIsSuccessful();
        self::assertSame($expected, (int) $response->getContent());
    }
}
