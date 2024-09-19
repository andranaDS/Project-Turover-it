<?php

namespace App\User\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;

class UserGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();

        $client->request('GET', '/users/6');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonEquals([
            '@context' => '/contexts/User',
            '@id' => '/users/6',
            '@type' => 'User',
            'id' => 6,
            'nickname' => 'Claude-Monet',
            'nicknameSlug' => 'claude-monet',
            'jobTitle' => 'Peintre',
            'website' => 'https://fr.wikipedia.org/wiki/Claude_Monet',
            'signature' => 'Claude Monet.',
            'avatar' => [
                'xSmall' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_x_small/monet-avatar.jpg',
                'small' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_small/monet-avatar.jpg',
                'medium' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_medium/monet-avatar.jpg',
            ],
            'displayAvatar' => true,
            'forumPostUpvotesCount' => 2,
            'forumPostsCount' => 7,
            'createdAt' => '2020-01-01T10:00:00+01:00',
            'blacklistedCompanies' => [],
            'deleted' => false,
            'forumRank' => 0,
        ]);

        $client->request('GET', '/users/7');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonEquals([
            '@context' => '/contexts/User',
            '@id' => '/users/7',
            '@type' => 'User',
            'id' => 7,
            'nickname' => 'Vincent-van-Gogh',
            'nicknameSlug' => 'vincent-van-gogh',
            'jobTitle' => null,
            'website' => null,
            'signature' => null,
            'avatar' => null,
            'displayAvatar' => false,
            'forumPostUpvotesCount' => 0,
            'forumPostsCount' => 6,
            'createdAt' => '2020-01-01T10:00:00+01:00',
            'blacklistedCompanies' => [],
            'deleted' => false,
            'forumRank' => 0,
        ]);
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();

        $client->request('GET', '/users/6');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/User',
            '@id' => '/users/6',
            '@type' => 'User',
            'id' => 6,
            'nickname' => 'Claude-Monet',
            'nicknameSlug' => 'claude-monet',
            'jobTitle' => 'Peintre',
            'website' => 'https://fr.wikipedia.org/wiki/Claude_Monet',
            'signature' => 'Claude Monet.',
            'avatar' => [
                'xSmall' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_x_small/monet-avatar.jpg',
                'small' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_small/monet-avatar.jpg',
                'medium' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_medium/monet-avatar.jpg',
            ],
            'displayAvatar' => true,
            'forumPostUpvotesCount' => 2,
            'forumPostsCount' => 7,
            'createdAt' => '2020-01-01T10:00:00+01:00',
            'deleted' => false,
            'forumRank' => 0,
        ]);
    }

    public function testLoggedAsUserOnItsOwnEntity(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/users/6');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/User',
            '@id' => '/users/6',
            '@type' => 'User',
            'id' => 6,
            'nickname' => 'Claude-Monet',
            'nicknameSlug' => 'claude-monet',
            'jobTitle' => 'Peintre',
            'website' => 'https://fr.wikipedia.org/wiki/Claude_Monet',
            'signature' => 'Claude Monet.',
            'avatar' => [
                'xSmall' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_x_small/monet-avatar.jpg',
                'small' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_small/monet-avatar.jpg',
                'medium' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_medium/monet-avatar.jpg',
            ],
            'displayAvatar' => true,
            'forumPostUpvotesCount' => 2,
            'forumPostsCount' => 7,
            'createdAt' => '2020-01-01T10:00:00+01:00',
            'deleted' => false,
            'forumRank' => 0,
        ]);
    }

    public function testLoggedAsUserOnAnotherEntity(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/users/7');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonEquals([
            '@context' => '/contexts/User',
            '@id' => '/users/7',
            '@type' => 'User',
            'id' => 7,
            'nickname' => 'Vincent-van-Gogh',
            'nicknameSlug' => 'vincent-van-gogh',
            'jobTitle' => null,
            'website' => null,
            'signature' => null,
            'avatar' => null,
            'displayAvatar' => false,
            'forumPostUpvotesCount' => 0,
            'forumPostsCount' => 6,
            'createdAt' => '2020-01-01T10:00:00+01:00',
            'blacklistedCompanies' => [],
            'deleted' => false,
            'forumRank' => 0,
        ]);
    }

    public function testResetEmailRequestedAt(): void
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
        self::assertNotNull($user->getEmailRequestedAt());

        $client->request('GET', '/users/me');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/User',
            '@id' => '/users/13',
            '@type' => 'User',
            'id' => 13,
            'email' => $email,
            'nickname' => 'User-New-Email-With-Expired-Request-Free-Work',
            'nicknameSlug' => 'user-new-email-with-expired-request-free-work',
            'jobTitle' => null,
            'website' => null,
            'signature' => null,
            'avatar' => null,
            'displayAvatar' => false,
            'forumPostUpvotesCount' => 0,
            'forumPostsCount' => 0,
            'createdAt' => '2020-01-01T10:00:00+01:00',
            'deleted' => false,
            'emailRequestedAt' => null,
            'forumRank' => 0,
        ]);

        self::assertNull($user->getEmailRequestedAt());
    }
}
