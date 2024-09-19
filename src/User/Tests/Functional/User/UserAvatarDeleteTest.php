<?php

namespace App\User\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserAvatarDeleteTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('DELETE', '/users/1/avatar');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedOnOtherUser(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('DELETE', '/users/1/avatar');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsCurrentUser(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('DELETE', '/users/6/avatar');

        self::assertResponseStatusCodeSame(204);
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient(); // id 2
        $client->request('DELETE', '/users/6/avatar');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsAdminOnNonExistantUser(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient(); // id 2
        $client->request('DELETE', '/users/999/avatar');

        self::assertResponseStatusCodeSame(404);
    }

    public function testDeleteLoggedAsCurrentUser(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $file = new UploadedFile(
            'src/User/DataFixtures/files/user-avatar-1.jpg',
            'user-avatar-1.jpg',
            'image/jpeg',
        );

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();

        // 1 - before
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'claude.monet@free-work.fr',
        ]);
        self::assertNotNull($user);
        self::assertSame('monet-avatar.jpg', $user->getAvatar());

        // 2 - post file
        $client->request('POST', '/users/6/avatar', [
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                'files' => [
                    'file' => $file,
                ],
            ],
        ]);

        // 3 - after
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'claude.monet@free-work.fr',
        ]);

        self::assertNotNull($user);
        self::assertNotNull($user->getAvatar());

        // 4 - delete file
        $client->request('DELETE', '/users/6/avatar');
        self::assertResponseStatusCodeSame(204);

        // 5 - after
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'claude.monet@free-work.fr',
        ]);

        self::assertNotNull($user);
        self::assertNull($user->getAvatar());
    }
}
