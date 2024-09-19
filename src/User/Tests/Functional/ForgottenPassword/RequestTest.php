<?php

namespace App\User\Tests\Functional\ForgottenPassword;

use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;

class RequestTest extends ApiTestCase
{
    public function testWithExistingEmailAndWithoutRequest(): void
    {
        $client = static::createFreeWorkClient();

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        $em = $container->get('doctrine')->getManager();

        $email = 'user-forgotten-password-without-request@free-work.fr';

        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => $email,
        ]);

        self::assertNotNull($user);

        self::assertNull($user->getPasswordRequestedAt());
        self::assertNull($user->getConfirmationToken());

        $client->request('POST', '/forgotten_password/request', [
            'json' => [
                'email' => $email,
            ],
        ]);

        self::assertResponseIsSuccessful();

        self::assertNotNull($user->getPasswordRequestedAt());
        self::assertNotNull($user->getConfirmationToken());

        // email
        self::assertEmailCount(1);
        $email = self::getMailerMessage();
        self::assertNotNull($email);
        self::assertEmailHeaderSame($email, 'from', 'Free-Work <account@free-work.com>');
        self::assertEmailHeaderSame($email, 'to', 'user-forgotten-password-without-request@free-work.fr');
        self::assertEmailHeaderSame($email, 'subject', 'TEST: Réinitialisez votre mot de passe');
        self::assertEmailHeaderSame($email, 'X-Mailjet-Campaign', 'forgotten_password_reset');
        self::assertEmailTextBodyContains($email, 'Nous venons de recevoir une demande de changement de mot de passe de votre part.');
        self::assertEmailTextBodyContains($email, 'Réinitialisez votre mot de passe');
    }

    public function testWithExistingEmailAndExpiredRequest(): void
    {
        $client = static::createFreeWorkClient();

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        $em = $container->get('doctrine')->getManager();

        $email = 'user-forgotten-password-with-expired-request@free-work.fr';

        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => $email,
        ]);

        self::assertNotNull($user);

        $oldPasswordRequestedAt = $user->getPasswordRequestedAt();
        $oldConfirmationToken = $user->getConfirmationToken();

        self::assertNotNull($oldPasswordRequestedAt);
        self::assertNotNull($oldConfirmationToken);

        $client->request('POST', '/forgotten_password/request', [
            'json' => [
                'email' => $email,
            ],
        ]);

        self::assertResponseIsSuccessful();

        $newPasswordRequestedAt = $user->getPasswordRequestedAt();
        $newConfirmationToken = $user->getConfirmationToken();

        self::assertNotNull($newPasswordRequestedAt);
        self::assertNotNull($newConfirmationToken);
        self::assertNotSame($oldPasswordRequestedAt->getTimestamp(), $newPasswordRequestedAt->getTimestamp());
        self::assertNotSame($oldConfirmationToken, $newConfirmationToken);

        // email
        self::assertEmailCount(1);
    }

    public function testWithExistingEmailAndActiveRequest(): void
    {
        $client = static::createFreeWorkClient();

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        $em = $container->get('doctrine')->getManager();

        $email = 'user-forgotten-password-with-active-request@free-work.fr';

        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => $email,
        ]);

        self::assertNotNull($user);

        $oldPasswordRequestedAt = $user->getPasswordRequestedAt();
        $oldConfirmationToken = $user->getConfirmationToken();

        self::assertNotNull($oldPasswordRequestedAt);
        self::assertNotNull($oldConfirmationToken);

        $client->request('POST', '/forgotten_password/request', [
            'json' => [
                'email' => $email,
            ],
        ]);

        self::assertResponseIsSuccessful();

        $newPasswordRequestedAt = $user->getPasswordRequestedAt();
        $newConfirmationToken = $user->getConfirmationToken();

        self::assertNotNull($newPasswordRequestedAt);
        self::assertNotNull($newConfirmationToken);
        self::assertSame($oldPasswordRequestedAt->getTimestamp(), $newPasswordRequestedAt->getTimestamp());
        self::assertSame($oldConfirmationToken, $newConfirmationToken);

        self::assertEmailCount(0);
    }

    public function testWithNotExistingEmail(): void
    {
        $client = static::createFreeWorkClient();

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        $em = $container->get('doctrine')->getManager();

        $email = 'user-forgotten-password-not-existing@free-work.fr';

        /** @var ?User $user */
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => $email,
        ]);

        self::assertNull($user);

        $client->request('POST', '/forgotten_password/request', [
            'json' => [
                'email' => $email,
            ],
        ]);

        self::assertResponseIsSuccessful();

        self::assertEmailCount(0);
    }

    public function testWithInvalidEmail(): void
    {
        $client = static::createFreeWorkClient();

        $client->request('POST', '/forgotten_password/request', [
            'json' => [
                'email' => 'invalid-email',
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'email: Cette valeur n\'est pas une adresse email valide.',
            'violations' => [
                [
                    'propertyPath' => 'email',
                    'message' => 'Cette valeur n\'est pas une adresse email valide.',
                ],
            ],
        ]);
    }

    public function testWithEmptyEmail(): void
    {
        $client = static::createFreeWorkClient();

        $client->request('POST', '/forgotten_password/request', [
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
}
