<?php

namespace App\Forum\Tests\Functional\ForumPost;

use App\Tests\Functional\ApiTestCase;

class ForumPostsTopicRepliesTest extends ApiTestCase
{
    public function testWithoutChildren(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/forum_topics/category-1-1-topic-2/replies');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumPost',
            '@id' => '/forum_posts',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/forum_posts/4',
                    '@type' => 'ForumPost',
                    'id' => 4,
                    'children' => [
                        [
                            '@id' => '/forum_posts/5',
                            '@type' => 'ForumPost',
                            'id' => 5,
                            'children' => [],
                            'author' => [
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
                                'deleted' => false,
                                'admin' => false,
                            ],
                            'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.1</p>',
                            'upvotesCount' => 1,
                            'createdAt' => '2021-01-10T19:45:00+01:00',
                            'updatedAt' => '2021-01-10T19:45:00+01:00',
                        ],
                        [
                            '@id' => '/forum_posts/6',
                            '@type' => 'ForumPost',
                            'id' => 6,
                            'children' => [
                                [
                                    '@id' => '/forum_posts/7',
                                    '@type' => 'ForumPost',
                                    'id' => 7,
                                    'children' => [],
                                    'author' => [
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
                                        'admin' => false,
                                    ],
                                    'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.2.1</p>',
                                    'upvotesCount' => 0,
                                    'createdAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTime::RFC3339),
                                    'updatedAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTime::RFC3339),
                                ],
                            ],
                            'author' => [
                                '@id' => '/users/8',
                                '@type' => 'User',
                                'id' => 8,
                                'nickname' => 'Auguste-Renoir',
                                'nicknameSlug' => 'auguste-renoir',
                                'jobTitle' => null,
                                'website' => null,
                                'signature' => null,
                                'avatar' => null,
                                'displayAvatar' => false,
                                'forumPostUpvotesCount' => 0,
                                'forumPostsCount' => 3,
                                'createdAt' => '2020-01-01T10:00:00+01:00',
                                'admin' => false,
                            ],
                            'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.2</p>',
                            'upvotesCount' => 0,
                            'createdAt' => '2021-01-10T20:30:00+01:00',
                            'updatedAt' => '2021-01-10T20:30:00+01:00',
                        ],
                        [
                            '@id' => '/forum_posts/8',
                            '@type' => 'ForumPost',
                            'id' => 8,
                            'children' => [],
                            'author' => [
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
                                'admin' => false,
                            ],
                            'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.3</p>',
                            'upvotesCount' => 0,
                            'createdAt' => (new \DateTime('today'))->setTime(0, 25)->format(\DateTime::RFC3339),
                            'updatedAt' => (new \DateTime('today'))->setTime(0, 25)->format(\DateTime::RFC3339),
                        ],
                    ],
                    'author' => [
                        '@id' => '/users/8',
                        '@type' => 'User',
                        'id' => 8,
                        'nickname' => 'Auguste-Renoir',
                        'nicknameSlug' => 'auguste-renoir',
                        'jobTitle' => null,
                        'website' => null,
                        'signature' => null,
                        'avatar' => null,
                        'displayAvatar' => false,
                        'forumPostUpvotesCount' => 0,
                        'forumPostsCount' => 3,
                        'createdAt' => '2020-01-01T10:00:00+01:00',
                        'admin' => false,
                    ],
                    'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2 - Lorem</p>',
                    'upvotesCount' => 0,
                    'createdAt' => '2021-01-10T19:30:00+01:00',
                    'updatedAt' => '2021-01-10T19:30:00+01:00',
                ],
                [
                    '@id' => '/forum_posts/9',
                    '@type' => 'ForumPost',
                    'id' => 9,
                    'children' => [],
                    'author' => [
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
                        'admin' => false,
                    ],
                    'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 3</p>',
                    'upvotesCount' => 0,
                    'createdAt' => '2021-01-10T20:30:00+01:00',
                    'updatedAt' => '2021-01-10T20:30:00+01:00',
                ],
            ],
            'hydra:totalItems' => 2,
        ]);
    }

    public function testWithChildren(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/forum_topics/category-1-1-topic-2/replies');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumPost',
            '@id' => '/forum_posts',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/forum_posts/4',
                    '@type' => 'ForumPost',
                    'id' => 4,
                    'children' => [
                        [
                            '@id' => '/forum_posts/5',
                            '@type' => 'ForumPost',
                            'id' => 5,
                            'children' => [],
                            'author' => [
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
                                'admin' => false,
                            ],
                            'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.1</p>',
                            'upvotesCount' => 1,
                            'createdAt' => '2021-01-10T19:45:00+01:00',
                            'updatedAt' => '2021-01-10T19:45:00+01:00',
                        ],
                        [
                            '@id' => '/forum_posts/6',
                            '@type' => 'ForumPost',
                            'id' => 6,
                            'children' => [
                                [
                                    '@id' => '/forum_posts/7',
                                    '@type' => 'ForumPost',
                                    'id' => 7,
                                    'children' => [],
                                    'author' => [
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
                                        'admin' => false,
                                    ],
                                    'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.2.1</p>',
                                    'upvotesCount' => 0,
                                    'createdAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTime::RFC3339),
                                    'updatedAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTime::RFC3339),
                                ],
                            ],
                            'author' => [
                                '@id' => '/users/8',
                                '@type' => 'User',
                                'id' => 8,
                                'nickname' => 'Auguste-Renoir',
                                'nicknameSlug' => 'auguste-renoir',
                                'jobTitle' => null,
                                'website' => null,
                                'signature' => null,
                                'avatar' => null,
                                'displayAvatar' => false,
                                'forumPostUpvotesCount' => 0,
                                'forumPostsCount' => 3,
                                'createdAt' => '2020-01-01T10:00:00+01:00',
                                'admin' => false,
                            ],
                            'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.2</p>',
                            'upvotesCount' => 0,
                            'createdAt' => '2021-01-10T20:30:00+01:00',
                            'updatedAt' => '2021-01-10T20:30:00+01:00',
                        ],
                        [
                            '@id' => '/forum_posts/8',
                            '@type' => 'ForumPost',
                            'id' => 8,
                            'children' => [],
                            'author' => [
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
                                'admin' => false,
                            ],
                            'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.3</p>',
                            'upvotesCount' => 0,
                            'createdAt' => (new \DateTime('today'))->setTime(0, 25)->format(\DateTime::RFC3339),
                            'updatedAt' => (new \DateTime('today'))->setTime(0, 25)->format(\DateTime::RFC3339),
                        ],
                    ],
                    'author' => [
                        '@id' => '/users/8',
                        '@type' => 'User',
                        'id' => 8,
                        'nickname' => 'Auguste-Renoir',
                        'nicknameSlug' => 'auguste-renoir',
                        'jobTitle' => null,
                        'website' => null,
                        'signature' => null,
                        'avatar' => null,
                        'displayAvatar' => false,
                        'forumPostUpvotesCount' => 0,
                        'forumPostsCount' => 3,
                        'createdAt' => '2020-01-01T10:00:00+01:00',
                        'admin' => false,
                    ],
                    'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2 - Lorem</p>',
                    'upvotesCount' => 0,
                    'createdAt' => '2021-01-10T19:30:00+01:00',
                    'updatedAt' => '2021-01-10T19:30:00+01:00',
                ],
                [
                    '@id' => '/forum_posts/9',
                    '@type' => 'ForumPost',
                    'id' => 9,
                    'children' => [],
                    'author' => [
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
                        'admin' => false,
                    ],
                    'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 3</p>',
                    'upvotesCount' => 0,
                    'createdAt' => '2021-01-10T20:30:00+01:00',
                    'updatedAt' => '2021-01-10T20:30:00+01:00',
                ],
            ],
            'hydra:totalItems' => 2,
        ]);
    }

    public function testWithPagination(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/forum_topics/category-1-1-topic-2/replies?itemsPerPage=1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumPost',
            '@id' => '/forum_posts',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/forum_posts/4',
                    '@type' => 'ForumPost',
                    'id' => 4,
                    'children' => [
                        [
                            '@id' => '/forum_posts/5',
                            '@type' => 'ForumPost',
                            'id' => 5,
                            'children' => [],
                            'author' => [
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
                                'admin' => false,
                            ],
                            'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.1</p>',
                            'upvotesCount' => 1,
                            'createdAt' => '2021-01-10T19:45:00+01:00',
                            'updatedAt' => '2021-01-10T19:45:00+01:00',
                        ],
                        [
                            '@id' => '/forum_posts/6',
                            '@type' => 'ForumPost',
                            'id' => 6,
                            'children' => [
                                [
                                    '@id' => '/forum_posts/7',
                                    '@type' => 'ForumPost',
                                    'id' => 7,
                                    'children' => [],
                                    'author' => [
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
                                        'admin' => false,
                                    ],
                                    'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.2.1</p>',
                                    'upvotesCount' => 0,
                                    'createdAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTime::RFC3339),
                                    'updatedAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTime::RFC3339),
                                ],
                            ],
                            'author' => [
                                '@id' => '/users/8',
                                '@type' => 'User',
                                'id' => 8,
                                'nickname' => 'Auguste-Renoir',
                                'nicknameSlug' => 'auguste-renoir',
                                'jobTitle' => null,
                                'website' => null,
                                'signature' => null,
                                'avatar' => null,
                                'displayAvatar' => false,
                                'forumPostUpvotesCount' => 0,
                                'forumPostsCount' => 3,
                                'createdAt' => '2020-01-01T10:00:00+01:00',
                                'admin' => false,
                            ],
                            'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.2</p>',
                            'upvotesCount' => 0,
                            'createdAt' => '2021-01-10T20:30:00+01:00',
                            'updatedAt' => '2021-01-10T20:30:00+01:00',
                        ],
                        [
                            '@id' => '/forum_posts/8',
                            '@type' => 'ForumPost',
                            'id' => 8,
                            'children' => [],
                            'author' => [
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
                                'admin' => false,
                            ],
                            'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.3</p>',
                            'upvotesCount' => 0,
                            'createdAt' => (new \DateTime('today'))->setTime(0, 25)->format(\DateTime::RFC3339),
                            'updatedAt' => (new \DateTime('today'))->setTime(0, 25)->format(\DateTime::RFC3339),
                        ],
                    ],
                    'author' => [
                        '@id' => '/users/8',
                        '@type' => 'User',
                        'id' => 8,
                        'nickname' => 'Auguste-Renoir',
                        'nicknameSlug' => 'auguste-renoir',
                        'jobTitle' => null,
                        'website' => null,
                        'signature' => null,
                        'avatar' => null,
                        'displayAvatar' => false,
                        'forumPostUpvotesCount' => 0,
                        'forumPostsCount' => 3,
                        'createdAt' => '2020-01-01T10:00:00+01:00',
                        'admin' => false,
                    ],
                    'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2 - Lorem</p>',
                    'upvotesCount' => 0,
                    'createdAt' => '2021-01-10T19:30:00+01:00',
                    'updatedAt' => '2021-01-10T19:30:00+01:00',
                ],
            ],
            'hydra:totalItems' => 2,
            'hydra:view' => [
                '@id' => '/forum_topics/category-1-1-topic-2/replies?itemsPerPage=1&page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/forum_topics/category-1-1-topic-2/replies?itemsPerPage=1&page=1',
                'hydra:last' => '/forum_topics/category-1-1-topic-2/replies?itemsPerPage=1&page=2',
                'hydra:next' => '/forum_topics/category-1-1-topic-2/replies?itemsPerPage=1&page=2',
            ],
        ]);
    }

    public function testWithDeleted(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/forum_topics/category-5-1-topic-1-lorem/replies');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumPost',
            '@id' => '/forum_posts',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/forum_posts/20',
                    '@type' => 'ForumPost',
                    'id' => 20,
                    'children' => [
                        [
                            '@id' => '/forum_posts/21',
                            '@type' => 'ForumPost',
                            'id' => 21,
                            'children' => [],
                            'author' => [
                                '@id' => '/users/11',
                                '@type' => 'User',
                                'id' => 11,
                                'nickname' => 'Free-Worker-11',
                                'nicknameSlug' => 'free-worker-11',
                                'jobTitle' => null,
                                'website' => null,
                                'signature' => null,
                                'avatar' => null,
                                'displayAvatar' => false,
                                'forumPostUpvotesCount' => 0,
                                'forumPostsCount' => 1,
                                'createdAt' => '2020-01-01T10:00:00+01:00',
                                'admin' => false,
                            ],
                            'contentHtml' => '<p>Category 5.1 - Topic 1 - Post 2.1</p>',
                            'upvotesCount' => 0,
                            'createdAt' => (new \DateTime('today'))->setTime(0, 10)->format(\DateTime::RFC3339),
                            'updatedAt' => (new \DateTime('today'))->setTime(0, 10)->format(\DateTime::RFC3339),
                            'deleted' => false,
                            'moderated' => false,
                        ],
                    ],
                    'author' => [
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
                        'admin' => false,
                    ],
                    'contentHtml' => null,
                    'upvotesCount' => 1,
                    'createdAt' => (new \DateTime('today'))->setTime(0, 5)->format(\DateTime::RFC3339),
                    'updatedAt' => (new \DateTime('today'))->setTime(0, 5)->format(\DateTime::RFC3339),
                    'deleted' => true,
                    'moderated' => false,
                ],
            ],
            'hydra:totalItems' => 1,
        ]);
    }
}
