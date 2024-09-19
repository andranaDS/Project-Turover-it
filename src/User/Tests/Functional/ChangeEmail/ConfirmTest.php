<?php

namespace App\User\Tests\Functional\ChangeEmail;

use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;

class ConfirmTest extends ApiTestCase
{
    public function testWithoutToken(): void
    {
        $client = static::createFreeWorkClient();

        $client->request('PATCH', '/change_email/confirm', [
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

        $client->request('PATCH', '/change_email/confirm', [
            'json' => [
                'token' => 'token-change-email-not-found',
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

        $client->request('PATCH', '/change_email/confirm', [
            'json' => [
                'email' => 'new-user-new-email-with-expired-request@free-work.fr',
                'token' => 'a1dacc3c1a3c93dcf7a5',
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains([
            'hydra:title' => 'Le lien de confirmation est expiré.',
        ]);
    }

    public function testWithActiveTokenAndWrongEmail(): void
    {
        $client = static::createFreeWorkClient();

        $client->request('PATCH', '/change_email/confirm', [
            'json' => [
                'email' => 'wrong-email@free-work.fr',
                'token' => '5e9e4e3910906a9a75c9',
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains([
            'hydra:title' => 'Le lien de confirmation est corrompu.',
        ]);
    }

    public function testWithActiveTokenAndGoodEmailNotLogged(): void
    {
        $client = static::createFreeWorkClient();

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        $em = $container->get('doctrine')->getManager();

        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneByConfirmationToken([
            'confirmationToken' => '5e9e4e3910906a9a75c9',
        ]);

        self::assertNotNull($user);

        // 1. before
        self::assertSame('user-new-email-with-active-request@free-work.fr', $user->getEmail());
        self::assertSame('5e9e4e3910906a9a75c9', $user->getConfirmationToken());
        self::assertNotNull($user->getEmailRequestedAt());

        // 2. change email
        $client->request('PATCH', '/change_email/confirm', [
            'json' => [
                'email' => 'new-user-new-email-with-active-request@free-work.fr',
                'token' => '5e9e4e3910906a9a75c9',
            ],
        ]);
        self::assertResponseIsSuccessful();

        // 3. after
        self::assertSame('new-user-new-email-with-active-request@free-work.fr', $user->getEmail());
        self::assertNull($user->getConfirmationToken());
        self::assertNull($user->getEmailRequestedAt());
    }
}
