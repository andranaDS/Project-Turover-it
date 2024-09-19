<?php

namespace App\Messaging\Tests\Functional\Feed;

use App\Messaging\Entity\FeedUser;
use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class FeedGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();

        $client->request('GET', '/feeds/1');
        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();

        $client->request('GET', '/feeds/1');
        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();

        $client->request('GET', '/feeds/1');
        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsOwner(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/feeds/1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public static function provideMandatoryDataCases(): iterable
    {
        return [
            [
                [
                    '@context' => '/contexts/Feed',
                    '@id' => '/feeds/1',
                    '@type' => 'Feed',
                    'id' => 1,
                    'application' => null,
                    'authorFeedUser' => [
                        '@type' => 'FeedUser',
                        'user' => [
                            '@type' => 'User',
                            'nickname' => 'Claude-Monet',
                            'nicknameSlug' => 'claude-monet',
                        ],
                        'favorite' => false,
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
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideMandatoryDataCases
     */
    public function testMandatoryData(array $expected): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();

        // 1 - before
        $beforeViewAt = null;
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'claude.monet@free-work.fr',
        ]);
        self::assertNotNull($user);
        /** @var FeedUser $feedUser */
        foreach ($user->getFeedUsers()->getValues() as $feedUser) {
            $feed = $feedUser->getFeed();
            if (null !== $feed && 1 === $feed->getId()) {
                $beforeViewAt = $feedUser->getViewAt();
            }
        }
        self::assertNotNull($beforeViewAt);

        // 2 - get feed
        $client->request('GET', '/feeds/1');

        // 3 - after
        $afterViewAt = null;
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'claude.monet@free-work.fr',
        ]);
        self::assertNotNull($user);
        /** @var FeedUser $feedUser */
        foreach ($user->getFeedUsers()->getValues() as $feedUser) {
            $feed = $feedUser->getFeed();
            if (null !== $feed && 1 === $feed->getId()) {
                $afterViewAt = $feedUser->getViewAt();
            }
        }
        self::assertNotNull($beforeViewAt);
        self::assertNotSame($beforeViewAt, $afterViewAt);

        self::assertJsonContains($expected);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}
