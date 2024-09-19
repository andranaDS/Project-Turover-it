<?php

namespace App\User\Tests\Functional\ChangeEmail;

use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;

class RequestTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('PATCH', '/change_email/request', [
            'json' => [
                'email' => 'new-email@free-work.fr',
            ],
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('PATCH', '/change_email/request', [
            'json' => [
                'email' => 'new-email@free-work.fr',
            ],
        ]);

        self::assertResponseIsSuccessful();
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('PATCH', '/change_email/request', [
            'json' => [
                'email' => 'new-email@free-work.fr',
            ],
        ]);

        self::assertResponseIsSuccessful();
    }

    public function testWithValidEmail(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        $em = $container->get('doctrine')->getManager();

        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'claude.monet@free-work.fr',
        ]);

        self::assertNotNull($user);

        self::assertSame('claude.monet@free-work.fr', $user->getEmail());
        self::assertNull($user->getEmailRequestedAt());
        self::assertNull($user->getConfirmationToken());

        $client->request('PATCH', '/change_email/request', [
            'json' => [
                'email' => 'new-email@free-work.fr',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertSame('claude.monet@free-work.fr', $user->getEmail());
        self::assertNotNull($user->getEmailRequestedAt());
        self::assertNotNull($user->getConfirmationToken());

        // email
        self::assertEmailCount(1);
        $email = self::getMailerMessage();
        self::assertNotNull($email);
        self::assertEmailHeaderSame($email, 'from', 'Free-Work <contact@free-work.com>');
        self::assertEmailHeaderSame($email, 'to', 'new-email@free-work.fr');
        self::assertEmailHeaderSame($email, 'subject', 'TEST: Demande de changement d\'adresse email');
        self::assertEmailHeaderSame($email, 'X-Mailjet-Campaign', 'change_email_request');
        self::assertEmailTextBodyContains($email, 'Vous venez de faire une demande de changement d\'adresse email dans votre compte Free-Work, afin que votre adresse email soit désormais : **new-email@free-work.fr**');
        self::assertEmailTextBodyContains($email, 'Je confirme');
    }

    public function testWithValidEmailAndExpiredRequest(): void
    {
        $email = 'user-new-email-with-expired-request@free-work.fr';
        $client = static::createFreeWorkAuthenticatedClient($email);

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        $em = $container->get('doctrine')->getManager();

        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => $email,
        ]);

        self::assertNotNull($user);

        $oldEmailRequestedAt = $user->getEmailRequestedAt();
        $oldConfirmationToken = $user->getConfirmationToken();

        self::assertNotNull($oldEmailRequestedAt);
        self::assertNotNull($oldConfirmationToken);

        $client->request('PATCH', '/change_email/request', [
            'json' => [
                'email' => 'new-email@free-work.fr',
            ],
        ]);

        self::assertResponseIsSuccessful();

        $newEmailRequestedAt = $user->getEmailRequestedAt();
        $newConfirmationToken = $user->getConfirmationToken();

        self::assertNotNull($newEmailRequestedAt);
        self::assertNotNull($newConfirmationToken);
        self::assertNotSame($oldEmailRequestedAt->getTimestamp(), $newEmailRequestedAt->getTimestamp());
        self::assertNotSame($oldConfirmationToken, $newConfirmationToken);

        // email
        self::assertEmailCount(1);
    }

    public function testWithMissingData(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('PATCH', '/change_email/request', [
            'json' => [
                'email' => '',
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'Cette valeur ne doit pas être vide.',
                ],
                [
                    'propertyPath' => 'email',
                    'message' => 'Cette valeur n\'est pas une adresse email valide.',
                ],
            ],
        ]);
    }

    public function testWithDuplicateEmail(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('PATCH', '/change_email/request', [
            'json' => [
                'email' => 'claude.monet@free-work.fr',
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'Cette valeur est déjà utilisée.',
                ],
            ],
        ]);
    }
}
