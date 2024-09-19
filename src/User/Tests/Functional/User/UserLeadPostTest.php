<?php

namespace App\User\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;

class UserLeadPostTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('POST', '/users/1/lead');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedOnOtherUser(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('POST', '/users/1/lead');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsNotCurrentUser(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('auguste.renoir@free-work.fr');
        $client->request('POST', '/users/6/lead', [
            'json' => [
                'formContent' => [],
                'isSuccess' => true,
                'partner' => 'freelancecom',
            ],
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    public static function provideLoggedAsCurrentUserCases(): iterable
    {
        yield [
            'auguste.renoir@free-work.fr',
            8,
            [
                'formContent' => [],
                'isSuccess' => true,
                'partner' => 'freelancecom',
            ],
        ];
    }

    /**
     * @dataProvider  provideLoggedAsCurrentUserCases
     */
    public function testLoggedAsCurrentUser(string $email, int $userId, array $payload): void
    {
        $client = static::createFreeWorkAuthenticatedClient($email);
        $client->request('POST', '/users/' . $userId . '/lead', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(400);
    }
}
