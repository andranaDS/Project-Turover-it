<?php

namespace App\User\Tests\Functional\Turnover\User;

use App\Tests\Functional\ApiTestCase;

class UserDeleteCandidatesTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('DELETE', '/users/42');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsNotOwner(): void
    {
        $client = static::createTurnoverAuthenticatedClient('robb.stark@got.com');
        $client->request('DELETE', '/users/42');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsOwner(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('DELETE', '/users/42');

        self::assertResponseStatusCodeSame(204);
    }
}
