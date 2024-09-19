<?php

namespace App\User\Tests\Functional\Security;

use App\Tests\Functional\ApiTestCase;

class LogoutTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/logout');

        self::assertResponseIsSuccessful();
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/logout');

        self::assertResponseIsSuccessful();
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/logout');

        self::assertResponseIsSuccessful();
    }

    public function testLogout(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $response = $client->request('GET', '/logout');

        self::assertResponseIsSuccessful();

        $cookies = $response->getHeaders()['set-cookie'] ?? [];
        sort($cookies);

        $cookies = array_map(static function (string $cookie) {
            $parts = array_map('trim', explode(';', $cookie));

            return $parts[0];
        }, $cookies);
        $cookiesCount = \count($cookies);

        self::assertSame(3, $cookiesCount);
        self::assertStringContainsString('deleted', $cookies[0]);
        self::assertStringContainsString('deleted', $cookies[1]);
        self::assertStringContainsString('deleted', $cookies[2]);
    }
}
