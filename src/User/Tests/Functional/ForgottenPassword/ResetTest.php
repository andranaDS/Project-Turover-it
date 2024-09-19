<?php

namespace App\User\Tests\Functional\ForgottenPassword;

use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;
use Symfony\Component\HttpFoundation\Response;

class ResetTest extends ApiTestCase
{
    public function testWithoutToken(): void
    {
        $client = static::createFreeWorkClient();

        $client->request('POST', '/forgotten_password/reset', [
            'json' => [],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains([
            'hydra:title' => 'Le lien de réinitialisation est expiré.',
        ]);
    }

    public function testWithNotFoundToken(): void
    {
        $client = static::createFreeWorkClient();

        $client->request('POST', '/forgotten_password/reset', [
            'json' => [
                'token' => 'token-not-found',
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains([
            'hydra:title' => 'Le lien de réinitialisation est expiré.',
        ]);
    }

    public function testWithExpiredToken(): void
    {
        $client = static::createFreeWorkClient();

        $client->request('POST', '/forgotten_password/reset', [
            'json' => [
                'token' => 'token-expired',
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains([
            'hydra:title' => 'Le lien de réinitialisation est expiré.',
        ]);
    }

    public function testWithActiveTokenAndWithoutPassword(): void
    {
        $client = static::createFreeWorkClient();

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        $em = $container->get('doctrine')->getManager();

        $confirmationToken = 'token-active';

        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneByConfirmationToken([
            'confirmationToken' => $confirmationToken,
        ]);

        $oldPassword = $user->getPassword();

        self::assertNotNull($user);

        $client->request('POST', '/forgotten_password/reset', [
            'json' => [
                'token' => $confirmationToken,
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertJsonContains([
            '@context' => '/contexts/User',
            '@id' => '/users/3',
            '@type' => 'User',
            'id' => 3,
            'email' => 'user-forgotten-password-with-active-request@free-work.fr',
            'nickname' => 'User-Forgotten-Password-With-Active-Request-Free-Work',
            'nicknameSlug' => 'user-forgotten-password-with-active-request-free-work',
            'jobTitle' => null,
            'website' => null,
            'signature' => null,
            'avatar' => null,
            'displayAvatar' => false,
            'forumPostUpvotesCount' => 0,
            'forumPostsCount' => 0,
            'createdAt' => '2020-01-01T10:00:00+01:00',
            'deleted' => false,
        ]);

        // check if the password has been updated
        $newPassword = $user->getPassword();
        self::assertSame($oldPassword, $newPassword);
    }

    public function testWithActiveTokenAndValidPassword(): void
    {
        $client = static::createFreeWorkClient();

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        $em = $container->get('doctrine')->getManager();

        $confirmationToken = 'token-active';

        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneByConfirmationToken([
            'confirmationToken' => $confirmationToken,
        ]);

        $oldPassword = $user->getPassword();

        self::assertNotNull($user);

        $client->request('POST', '/forgotten_password/reset', [
            'json' => [
                'token' => $confirmationToken,
                'plainPassword' => 'NewP@ssw0rd1',
            ],
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertJsonContains([
            '@context' => '/contexts/User',
            '@id' => '/users/3',
            '@type' => 'User',
            'id' => 3,
            'email' => 'user-forgotten-password-with-active-request@free-work.fr',
            'nickname' => 'User-Forgotten-Password-With-Active-Request-Free-Work',
            'nicknameSlug' => 'user-forgotten-password-with-active-request-free-work',
            'jobTitle' => null,
            'website' => null,
            'signature' => null,
            'avatar' => null,
            'displayAvatar' => false,
            'forumPostUpvotesCount' => 0,
            'forumPostsCount' => 0,
            'createdAt' => '2020-01-01T10:00:00+01:00',
            'deleted' => false,
        ]);

        // check if the password has been updated
        $newPassword = $user->getPassword();
        self::assertNotSame($oldPassword, $newPassword);
    }

    public function testWithActiveTokenAndInvalidPassword(): void
    {
        $client = static::createFreeWorkClient();

        $client->request('POST', '/forgotten_password/reset', [
            'json' => [
                'token' => 'token-active',
                'plainPassword' => 'password',
            ],
        ]);

        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'violations' => [
                [
                    'propertyPath' => 'plainPassword',
                    'message' => 'La force du mot de passe doit être au minimum "Bon".',
                ],
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
    }
}
