<?php

namespace App\Messaging\Tests\Functional\Feed;

use App\Messaging\Entity\FeedUser;
use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class FeedPutFavoriteTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();

        $client->request('PUT', '/feeds/1/favorite');
        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('PUT', '/feeds/1/favorite');

        self::assertResponseStatusCodeSame(200);
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient(); // id 2
        $client->request('PUT', '/feeds/1/favorite');

        self::assertResponseStatusCodeSame(401);
    }

    public function testValidData(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

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
        $feedUser = $em->getRepository(FeedUser::class)->findOneById(1);
        self::assertNotNull($feedUser);
        self::assertFalse($feedUser->getFavorite());

        // 2 - put favorite
        $client->request('PUT', '/feeds/1/favorite');

        // 3 - after
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'claude.monet@free-work.fr',
        ]);
        self::assertNotNull($user);
        $feedUser = $em->getRepository(FeedUser::class)->findOneById(1);
        self::assertNotNull($feedUser);
        self::assertTrue($feedUser->getFavorite());

        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Feed',
            '@id' => '/feeds/1',
            '@type' => 'Feed',
            'id' => 1,
            'application' => null,
            'messages' => [
                [
                    '@type' => 'Message',
                    'id' => 3,
                    'content' => 'Feed 1 - Message 3 - Content New',
                    'contentHtml' => '<p>Feed 1 - Message 3 - Content New</p>',
                    'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Feed 1 - Message 3 - Content New"}]}]}',
                    'author' => [
                        '@type' => 'User',
                        'id' => 7,
                        'nickname' => 'Vincent-van-Gogh',
                        'nicknameSlug' => 'vincent-van-gogh',
                        'displayAvatar' => false,
                        'avatar' => null,
                        'deleted' => false,
                    ],
                    'createdAt' => '2021-02-10T20:00:00+01:00',
                ],
                [
                    '@type' => 'Message',
                    'id' => 2,
                    'content' => 'Feed 1 - Message 2 - Content',
                    'contentHtml' => '<p>Feed 1 - Message 2 - Content</p>',
                    'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Feed 1 - Message 2 - Content"}]}]}',
                    'author' => [
                        '@type' => 'User',
                        'id' => 6,
                        'nickname' => 'Claude-Monet',
                        'nicknameSlug' => 'claude-monet',
                        'displayAvatar' => true,
                        'avatar' => [
                            'xSmall' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_x_small/monet-avatar.jpg',
                            'small' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_small/monet-avatar.jpg',
                            'medium' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_medium/monet-avatar.jpg',
                        ],
                        'deleted' => false,
                    ],
                    'createdAt' => '2021-01-10T21:00:00+01:00',
                ],
            ],
            'authorFeedUser' => [
                '@type' => 'FeedUser',
                'user' => [
                    '@type' => 'User',
                    'nickname' => 'Claude-Monet',
                    'nicknameSlug' => 'claude-monet',
                ],
                'favorite' => true,
            ],
            'receiverFeedUser' => [
                '@type' => 'FeedUser',
                'user' => [
                    '@type' => 'User',
                    'nickname' => 'Vincent-van-Gogh',
                    'nicknameSlug' => 'vincent-van-gogh',
                ],
                'favorite' => false,
            ],
        ]);

        // 4 - put favorite
        $client->request('PUT', '/feeds/1/favorite');

        // 5 - after
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'claude.monet@free-work.fr',
        ]);
        self::assertNotNull($user);
        $feedUser = $em->getRepository(FeedUser::class)->findOneById(1);
        self::assertNotNull($feedUser);
        self::assertFalse($feedUser->getFavorite());

        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Feed',
            '@id' => '/feeds/1',
            '@type' => 'Feed',
            'id' => 1,
            'application' => null,
            'messages' => [],
            'authorFeedUser' => [
                '@type' => 'FeedUser',
                'user' => [],
                'favorite' => false,
            ],
            'receiverFeedUser' => [],
        ]);
    }
}
