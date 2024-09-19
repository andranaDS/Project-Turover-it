<?php

namespace App\Messaging\Tests\Functional\Feed;

use App\Tests\Functional\ApiTestCase;

class FeedsGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();

        $client->request('GET', '/feeds');
        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();

        $client->request('GET', '/feeds');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();

        $client->request('GET', '/feeds');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public static function provideDefaultOrderDataCases(): iterable
    {
        return [
            [
                [
                    '@context' => '/contexts/Feed',
                    '@id' => '/feeds',
                    '@type' => 'hydra:Collection',
                    'hydra:member' => [
                        [
                            '@type' => 'Feed',
                            'id' => 1,
                            'application' => null,
                            'lastMessage' => [
                                '@type' => 'Message',
                                'id' => 3,
                                'content' => 'Feed 1 - Message 3 - Content New',
                                'author' => [
                                    '@type' => 'User',
                                    'id' => 7,
                                    'nickname' => 'Vincent-van-Gogh',
                                    'nicknameSlug' => 'vincent-van-gogh',
                                    'firstName' => 'Vincent',
                                    'lastName' => 'van Gogh',
                                    'displayAvatar' => false,
                                    'avatar' => null,
                                    'deleted' => false,
                                ],
                                'createdAt' => '2021-02-10T20:00:00+01:00',
                            ],
                            'authorFeedUser' => [
                                '@type' => 'FeedUser',
                                'favorite' => false,
                                'viewAt' => '2021-01-10T23:00:00+01:00',
                                'user' => [
                                    '@type' => 'User',
                                    'nickname' => 'Claude-Monet',
                                    'nicknameSlug' => 'claude-monet',
                                ],
                            ],
                            'receiverFeedUser' => [
                                '@type' => 'FeedUser',
                                'favorite' => false,
                                'viewAt' => '2021-02-10T20:00:00+01:00',
                                'user' => [
                                    '@type' => 'User',
                                    'nickname' => 'Vincent-van-Gogh',
                                    'nicknameSlug' => 'vincent-van-gogh',
                                ],
                            ],
                        ],
                        [
                            '@type' => 'Feed',
                            'id' => 3,
                            'application' => [
                                '@type' => 'Application',
                                'id' => 1,
                            ],
                            'lastMessage' => [
                                '@type' => 'Message',
                                'id' => 7,
                                'content' => 'Feed 3 - Message 1',
                                'author' => [
                                    '@type' => 'User',
                                    'id' => 6,
                                    'nickname' => 'Claude-Monet',
                                    'nicknameSlug' => 'claude-monet',
                                    'firstName' => 'Claude',
                                    'lastName' => 'Monet',
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
                            'authorFeedUser' => [
                                '@type' => 'FeedUser',
                                'favorite' => false,
                                'viewAt' => '2021-03-10T20:00:00+01:00',
                                'user' => [
                                    '@type' => 'User',
                                    'nickname' => 'Claude-Monet',
                                    'nicknameSlug' => 'claude-monet',
                                ],
                            ],
                            'receiverFeedUser' => [
                                '@type' => 'FeedUser',
                                'favorite' => true,
                                'viewAt' => '2021-04-10T20:00:00+02:00',
                                'user' => [
                                    '@type' => 'User',
                                    'nickname' => 'Henri-Matisse',
                                    'nicknameSlug' => 'henri-matisse',
                                ],
                            ],
                        ],
                    ],
                    'hydra:totalItems' => 3,
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideDefaultOrderDataCases
     */
    public function testDefaultOrderData(array $expected): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/feeds');

        self::assertJsonContains($expected);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public static function provideSearchDataWithResultCases(): iterable
    {
        return [
            [
                [
                    '@context' => '/contexts/Feed',
                    '@id' => '/feeds',
                    '@type' => 'hydra:Collection',
                    'hydra:member' => [
                        [
                            '@type' => 'Feed',
                            'id' => 2,
                            'application' => null,
                            'lastMessage' => [
                                '@type' => 'Message',
                                'id' => 6,
                                'content' => 'Feed 2 - Message 3 - Content Not New',
                                'author' => [
                                ],
                                'createdAt' => '2021-01-10T17:00:00+01:00',
                            ],
                            'authorFeedUser' => [
                                '@type' => 'FeedUser',
                                'favorite' => true,
                                'viewAt' => '2021-01-10T23:00:00+01:00',
                            ],
                            'receiverFeedUser' => [
                                '@type' => 'FeedUser',
                                'favorite' => false,
                                'viewAt' => '2021-01-10T19:00:00+01:00',
                            ],
                        ],
                    ],
                    'hydra:totalItems' => 1,
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideSearchDataWithResultCases
     */
    public function testSearchDataWithResult(array $expected): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/feeds?q=renoir');

        self::assertJsonContains($expected);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public static function provideSearchDataWithoutResultCases(): iterable
    {
        return [
            [
                [
                    '@context' => '/contexts/Feed',
                    '@id' => '/feeds',
                    '@type' => 'hydra:Collection',
                    'hydra:member' => [],
                    'hydra:totalItems' => 0,
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideSearchDataWithoutResultCases
     */
    public function testSearchDataWithoutResult(array $expected): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/feeds?q=wxc');

        self::assertJsonContains($expected);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public static function provideUnreadOrderDataCases(): iterable
    {
        return [
            [
                [
                    '@context' => '/contexts/Feed',
                    '@id' => '/feeds',
                    '@type' => 'hydra:Collection',
                    'hydra:member' => [
                        [
                            '@type' => 'Feed',
                            'id' => 1,
                            'application' => null,
                            'lastMessage' => [
                                '@type' => 'Message',
                                'id' => 3,
                                'content' => 'Feed 1 - Message 3 - Content New',
                                'author' => [
                                    '@type' => 'User',
                                    'id' => 7,
                                    'nickname' => 'Vincent-van-Gogh',
                                    'nicknameSlug' => 'vincent-van-gogh',
                                    'firstName' => 'Vincent',
                                    'lastName' => 'van Gogh',
                                    'displayAvatar' => false,
                                    'avatar' => null,
                                    'deleted' => false,
                                ],
                                'createdAt' => '2021-02-10T20:00:00+01:00',
                            ],
                            'authorFeedUser' => [
                                '@type' => 'FeedUser',
                                'favorite' => false,
                                'viewAt' => '2021-01-10T23:00:00+01:00',
                            ],
                            'receiverFeedUser' => [
                                '@type' => 'FeedUser',
                                'favorite' => false,
                                'viewAt' => '2021-02-10T20:00:00+01:00',
                            ],
                        ],
                        [
                            '@type' => 'Feed',
                            'id' => 3,
                            'application' => [
                                '@type' => 'Application',
                                'id' => 1,
                            ],
                            'lastMessage' => [
                                '@type' => 'Message',
                                'id' => 7,
                                'content' => 'Feed 3 - Message 1',
                                'author' => [
                                    '@type' => 'User',
                                    'id' => 6,
                                    'nickname' => 'Claude-Monet',
                                    'nicknameSlug' => 'claude-monet',
                                    'firstName' => 'Claude',
                                    'lastName' => 'Monet',
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
                            'authorFeedUser' => [
                                '@type' => 'FeedUser',
                                'favorite' => false,
                                'viewAt' => '2021-03-10T20:00:00+01:00',
                            ],
                            'receiverFeedUser' => [
                                '@type' => 'FeedUser',
                                'favorite' => true,
                                'viewAt' => '2021-04-10T20:00:00+02:00',
                            ],
                        ],
                    ],
                    'hydra:totalItems' => 3,
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideUnreadOrderDataCases
     */
    public function testUnreadOrderData(array $expected): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/feeds/order/unread');

        self::assertJsonContains($expected);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public static function provideFavoriteOrderDataCases(): iterable
    {
        return [
            [
                [
                    '@context' => '/contexts/Feed',
                    '@id' => '/feeds',
                    '@type' => 'hydra:Collection',
                    'hydra:member' => [
                        [
                            '@type' => 'Feed',
                            'id' => 2,
                            'application' => null,
                            'lastMessage' => [
                                '@type' => 'Message',
                                'id' => 6,
                                'content' => 'Feed 2 - Message 3 - Content Not New',
                                'author' => [
                                ],
                                'createdAt' => '2021-01-10T17:00:00+01:00',
                            ],
                            'authorFeedUser' => [
                                '@type' => 'FeedUser',
                                'favorite' => true,
                                'viewAt' => '2021-01-10T23:00:00+01:00',
                            ],
                            'receiverFeedUser' => [
                                '@type' => 'FeedUser',
                                'favorite' => false,
                                'viewAt' => '2021-01-10T19:00:00+01:00',
                            ],
                        ],
                        [
                            '@type' => 'Feed',
                            'id' => 1,
                            'application' => null,
                            'lastMessage' => [
                                '@type' => 'Message',
                                'id' => 3,
                                'content' => 'Feed 1 - Message 3 - Content New',
                                'author' => [
                                    '@type' => 'User',
                                    'id' => 7,
                                    'nickname' => 'Vincent-van-Gogh',
                                    'nicknameSlug' => 'vincent-van-gogh',
                                    'firstName' => 'Vincent',
                                    'lastName' => 'van Gogh',
                                    'displayAvatar' => false,
                                    'avatar' => null,
                                    'deleted' => false,
                                ],
                                'createdAt' => '2021-02-10T20:00:00+01:00',
                            ],
                            'authorFeedUser' => [
                                '@type' => 'FeedUser',
                                'favorite' => false,
                                'viewAt' => '2021-01-10T23:00:00+01:00',
                            ],
                            'receiverFeedUser' => [
                                '@type' => 'FeedUser',
                                'favorite' => false,
                                'viewAt' => '2021-02-10T20:00:00+01:00',
                            ],
                        ],
                    ],
                    'hydra:totalItems' => 3,
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideFavoriteOrderDataCases
     */
    public function testFavoriteOrderData(array $expected): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/feeds/order/favorite');

        self::assertJsonContains($expected);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}
