<?php

namespace App\User\Tests\Functional\Registration;

use App\Tests\Functional\ApiTestCase;

class ConfirmTest extends ApiTestCase
{
    public function testWithoutToken(): void
    {
        $client = static::createFreeWorkClient();

        $client->request('POST', '/registration/confirm', [
            'json' => [],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains([
            'hydra:title' => 'Le lien de confirmation est expiré.',
        ]);
    }

    public function testWithNotFoundToken(): void
    {
        $client = static::createFreeWorkClient();

        $client->request('POST', '/registration/confirm', [
            'json' => [
                'token' => 'token-email-not-found',
            ],
        ]);
        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains([
            'hydra:title' => 'Le lien de confirmation est expiré.',
        ]);
    }

    public function testWithExpiredToken(): void
    {
        $client = static::createFreeWorkClient();

        // check if the user is in the database
        $client->request('GET', '/users/27');
        self::assertResponseIsSuccessful();

        // try to confirm the email
        $client = static::createFreeWorkClient();
        $client->request('POST', '/registration/confirm', [
            'json' => [
                'token' => 'email-confirm-token-expired',
            ],
        ]);
        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains([
            'hydra:title' => 'Le lien de confirmation est expiré.',
        ]);

        // check if the user has been deleted from the database
        $client->request('GET', '/users/27');
        self::assertResponseStatusCodeSame(404);
    }

    public function testWithValidToken(): void
    {
        $client = static::createFreeWorkClient();

        $response = $client->request('POST', '/registration/confirm', [
            'json' => [
                'token' => 'email-confirm-token-active',
            ],
        ]);

        // response
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@type' => 'User',
            'id' => 28,
        ]);

        // email
        $email = self::getMailerMessage(1);
        self::assertNotNull($email);
        self::assertEmailHeaderSame($email, 'from', 'Free-Work <account@free-work.com>');
        self::assertEmailHeaderSame($email, 'to', 'user-to-enable-with-active-token@free-work.fr');
        self::assertEmailHeaderSame($email, 'subject', 'TEST: Félicitations, votre compte Free-Work est créé !');
        self::assertEmailHeaderSame($email, 'X-Mailjet-Campaign', 'registration_welcome');
        self::assertEmailTextBodyContains($email, 'Aussi, vous pouvez désormais interagir avec la communauté de Free-workers à travers le Forum et le Blog !');

        // logged
        $cookies = self::extractCookies($response);

        $cookiesCount = \count($cookies);
        self::assertSame(3, $cookiesCount);
        self::assertEqualsWithDelta(3600, (int) $cookies[0], 2);
        self::assertEqualsWithDelta(3600, (int) $cookies[1], 2);
        self::assertEqualsWithDelta(86400, (int) $cookies[2], 2);

        // enable
        $client->request('GET', '/users/me');
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'enabled' => true,
        ]);
    }
}
