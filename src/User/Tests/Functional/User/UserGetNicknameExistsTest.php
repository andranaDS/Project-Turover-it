<?php

namespace App\User\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;

class UserGetNicknameExistsTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/users/exists/Henri-Matisse');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/users/exists/Henri-Matisse');

        self::assertResponseStatusCodeSame(200);
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('GET', '/users/exists/Henri-Matisse');

        self::assertResponseStatusCodeSame(200);
    }

    public function testAvailableNickname(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/users/exists/AvailableNickname');

        self::assertResponseStatusCodeSame(404);
    }
}
