<?php

namespace App\User\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserAvatarPostTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('POST', '/users/1/avatar');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedOnOtherUser(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('POST', '/users/1/avatar');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsCurrentUser(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('POST', '/users/6/avatar');

        self::assertResponseStatusCodeSame(400);
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient(); // id 2
        $client->request('POST', '/users/6/avatar');

        self::assertResponseStatusCodeSame(403);
    }

    public function testBlankDataLoggedAsCurrentUser(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('POST', '/users/6/avatar', [
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                'files' => [
                    'file' => [],
                ],
            ],
        ]);

        self::assertResponseStatusCodeSame(400);
        self::assertJsonContains([
            '@context' => '/contexts/Error',
            '@type' => 'hydra:Error',
            'hydra:title' => 'An error occurred',
            'hydra:description' => '"file" is required',
        ]);
    }

    public function testValidDataLoggedAsCurrentUser(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $file = new UploadedFile(
            'src/User/DataFixtures/files/user-avatar-thierry-henry.jpg',
            'user-avatar-thierry-henry.jpg',
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
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // 3 - after
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'claude.monet@free-work.fr',
        ]);
        self::assertNotNull($user);
        $newAvatar = $user->getAvatar();
        self::assertNotNull($newAvatar);
        self::assertNotSame('monet-avatar.jpg', $newAvatar);

        self::assertJsonContains([
            '@context' => '/contexts/User',
            '@type' => 'User',
            'nickname' => 'Claude-Monet',
            'nicknameSlug' => 'claude-monet',
            'jobTitle' => 'Peintre',
            'website' => 'https://fr.wikipedia.org/wiki/Claude_Monet',
            'signature' => 'Claude Monet.',
            'avatar' => [
                'xSmall' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_x_small/' . $newAvatar,
                'small' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_small/' . $newAvatar,
                'medium' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_medium/' . $newAvatar,
            ],
            'displayAvatar' => true,
            'forumPostUpvotesCount' => 2,
            'forumPostsCount' => 7,
            'createdAt' => '2020-01-01T10:00:00+01:00',
            'deleted' => false,
        ]);
    }
}
