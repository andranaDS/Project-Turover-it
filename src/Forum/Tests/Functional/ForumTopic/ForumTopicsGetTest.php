<?php

namespace App\Forum\Tests\Functional\ForumTopic;

use App\Tests\Functional\ApiTestCase;

class ForumTopicsGetTest extends ApiTestCase
{
    public function testWithCategory1(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/forum_topics?category.slug=category-1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumTopic',
            '@id' => '/forum_topics',
            '@type' => 'hydra:Collection',
            'hydra:member' => [],
            'hydra:totalItems' => 0,
        ]);
    }

    public function testWithCategory2(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/forum_topics?category.slug=category-1-1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumTopic',
            '@id' => '/forum_topics',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@type' => 'ForumTopic',
                    'category' => [
                        '@type' => 'ForumCategory',
                        'title' => 'Category 1.1',
                        'slug' => 'category-1-1',
                    ],
                    'title' => 'Category 1.1 - Topic 2',
                    'slug' => 'category-1-1-topic-2',
                    'pinned' => false,
                    'lastPost' => [
                        '@type' => 'ForumPost',
                        'author' => [
                            '@type' => 'User',
                            'jobTitle' => null,
                            'website' => null,
                            'signature' => null,
                            'avatar' => null,
                            'displayAvatar' => false,
                            'nickname' => 'Vincent-van-Gogh',
                            'nicknameSlug' => 'vincent-van-gogh',
                            'forumPostUpvotesCount' => 0,
                            'forumPostsCount' => 6,
                            'createdAt' => '2020-01-01T10:00:00+01:00',
                            'deleted' => false,
                            'admin' => false,
                        ],
                        'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.2.1</p>',
                        'createdAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTimeInterface::RFC3339),
                        'updatedAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTimeInterface::RFC3339),
                    ],
                    'author' => [
                        '@type' => 'User',
                        'jobTitle' => null,
                        'website' => null,
                        'signature' => null,
                        'avatar' => null,
                        'displayAvatar' => false,
                        'nickname' => 'Vincent-van-Gogh',
                        'nicknameSlug' => 'vincent-van-gogh',
                        'forumPostUpvotesCount' => 0,
                        'forumPostsCount' => 6,
                        'createdAt' => '2020-01-01T10:00:00+01:00',
                        'deleted' => false,
                        'admin' => false,
                    ],
                ],
                [
                    '@type' => 'ForumTopic',
                    'category' => [
                        '@type' => 'ForumCategory',
                        'title' => 'Category 1.1',
                        'slug' => 'category-1-1',
                    ],
                    'title' => 'Category 1.1 - Topic 4',
                    'slug' => 'category-1-1-topic-4',
                    'pinned' => false,
                    'lastPost' => [
                        '@type' => 'ForumPost',
                        'author' => [
                            '@type' => 'User',
                            'jobTitle' => null,
                            'website' => null,
                            'signature' => null,
                            'avatar' => null,
                            'displayAvatar' => false,
                            'nickname' => 'Admin-Free-Work',
                            'nicknameSlug' => 'admin-free-work',
                            'forumPostUpvotesCount' => 0,
                            'forumPostsCount' => 1,
                            'createdAt' => '2020-01-01T10:00:00+01:00',
                            'admin' => false,
                        ],
                        'contentHtml' => '<p>Category 1.1 - Topic 4 - Post 1</p>',
                        'createdAt' => '2021-01-09T21:00:00+01:00',
                        'updatedAt' => '2021-01-09T21:00:00+01:00',
                    ],
                    'author' => [
                        '@type' => 'User',
                        'jobTitle' => null,
                        'website' => null,
                        'signature' => null,
                        'avatar' => null,
                        'displayAvatar' => false,
                        'nickname' => 'Admin-Free-Work',
                        'nicknameSlug' => 'admin-free-work',
                        'forumPostUpvotesCount' => 0,
                        'forumPostsCount' => 1,
                        'createdAt' => '2020-01-01T10:00:00+01:00',
                        'deleted' => false,
                        'admin' => false,
                    ],
                ],
            ],
            'hydra:totalItems' => 4,
            'hydra:view' => [
                '@id' => '/forum_topics?category.slug=category-1-1&page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/forum_topics?category.slug=category-1-1&page=1',
                'hydra:last' => '/forum_topics?category.slug=category-1-1&page=2',
                'hydra:next' => '/forum_topics?category.slug=category-1-1&page=2',
            ],
        ]);
    }

    public function testMostRecent(): void
    {
        $client = static::createFreeWorkClient();

        // 1. without order desc
        $client->request('GET', '/forum_topics?order[lastPost.createdAt]');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains([
            '@context' => '/contexts/ForumTopic',
            '@id' => '/forum_topics',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@type' => 'ForumTopic',
                    'title' => 'Category 1.1 - Topic 2',
                    'lastPost' => [
                        '@type' => 'ForumPost',
                        'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.2.1</p>',
                        'createdAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTimeInterface::RFC3339),
                        'updatedAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTimeInterface::RFC3339),
                    ],
                ],
                [
                    '@type' => 'ForumTopic',
                    'title' => 'Category 4.1 - Topic 1',
                    'lastPost' => [
                        '@type' => 'ForumPost',
                        'contentHtml' => '<p>Category 4.1 - Topic 1 - Post 2</p>',
                        'createdAt' => (new \DateTime('today'))->setTime(0, 20)->format(\DateTimeInterface::RFC3339),
                        'updatedAt' => (new \DateTime('today'))->setTime(0, 20)->format(\DateTimeInterface::RFC3339),
                    ],
                ],
            ],
        ]);

        // 2. with order desc
        $client->request('GET', '/forum_topics?order[lastPost.createdAt]=desc');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumTopic',
            '@id' => '/forum_topics',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@type' => 'ForumTopic',
                    'title' => 'Category 1.1 - Topic 2',
                    'lastPost' => [
                        '@type' => 'ForumPost',
                        'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.2.1</p>',
                        'createdAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTimeInterface::RFC3339),
                        'updatedAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTimeInterface::RFC3339),
                    ],
                ],
                [
                    '@type' => 'ForumTopic',
                    'title' => 'Category 4.1 - Topic 1',
                    'lastPost' => [
                        '@type' => 'ForumPost',
                        'contentHtml' => '<p>Category 4.1 - Topic 1 - Post 2</p>',
                        'createdAt' => (new \DateTime('today'))->setTime(0, 20)->format(\DateTimeInterface::RFC3339),
                        'updatedAt' => (new \DateTime('today'))->setTime(0, 20)->format(\DateTimeInterface::RFC3339),
                    ],
                ],
            ],
        ]);
    }

    public function testOldest(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/forum_topics?order[lastPost.createdAt]=asc');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumTopic',
            '@id' => '/forum_topics',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@type' => 'ForumTopic',
                    'title' => 'Category 1.1 - Topic 1',
                    'lastPost' => [
                        '@type' => 'ForumPost',
                        'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 2</p>',
                        'createdAt' => '2021-01-01T13:00:00+01:00',
                        'updatedAt' => '2021-01-01T13:00:00+01:00',
                    ],
                ],
                [
                    '@type' => 'ForumTopic',
                    'title' => 'Category 1.2 - Topic 1',
                    'lastPost' => [
                        '@type' => 'ForumPost',
                        'contentHtml' => '<p>Category 1.2 - Topic 1 - Post 1</p>',
                        'createdAt' => '2021-01-03T12:00:00+01:00',
                        'updatedAt' => '2021-01-03T12:00:00+01:00',
                    ],
                ],
            ],
        ]);
    }

    public function testSearchWithoutResults(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/forum_topics/search?q=zxz');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumTopic',
            '@id' => '/forum_topics',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 0,
        ]);
    }

    public function testSearchWithQueryOnTopicTitleWithResults(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/forum_topics/search?q=topic+1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumTopic',
            '@id' => '/forum_topics',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                0 => [
                    '@id' => '/forum_topics/category-4-1-topic-1',
                    '@type' => 'ForumTopic',
                    'id' => 7,
                    'category' => [
                        '@id' => '/forum_categories/category-4-1',
                        '@type' => 'ForumCategory',
                        'id' => 11,
                        'title' => 'Category 4.1',
                        'slug' => 'category-4-1',
                    ],
                    'title' => 'Category 4.1 - Topic 1',
                    'slug' => 'category-4-1-topic-1',
                    'pinned' => false,
                    'lastPost' => [
                        '@id' => '/forum_posts/18',
                        '@type' => 'ForumPost',
                        'id' => 18,
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
                            'deleted' => false,
                            'admin' => false,
                        ],
                        'contentHtml' => '<p>Category 4.1 - Topic 1 - Post 2</p>',
                        'upvotesCount' => 0,
                        'createdAt' => (new \DateTime('today'))->setTime(0, 20)->format(\DateTime::RFC3339),
                        'updatedAt' => (new \DateTime('today'))->setTime(0, 20)->format(\DateTime::RFC3339),
                        'deleted' => false,
                        'moderated' => false,
                    ],
                    'author' => [
                        '@id' => '/users/9',
                        '@type' => 'User',
                        'id' => 9,
                        'nickname' => 'Henri-Matisse',
                        'nicknameSlug' => 'henri-matisse',
                        'jobTitle' => null,
                        'website' => null,
                        'signature' => null,
                        'avatar' => [
                            'xSmall' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_x_small/matisse-avatar.jpg',
                            'small' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_small/matisse-avatar.jpg',
                            'medium' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_medium/matisse-avatar.jpg',
                        ],
                        'displayAvatar' => false,
                        'forumPostUpvotesCount' => 1,
                        'forumPostsCount' => 1,
                        'createdAt' => '2020-01-01T10:00:00+01:00',
                        'deleted' => false,
                        'admin' => false,
                    ],
                    'posts' => [
                        0 => [
                            '@id' => '/forum_posts/17',
                            '@type' => 'ForumPost',
                            'id' => 17,
                            'author' => [
                                '@id' => '/users/9',
                                '@type' => 'User',
                                'id' => 9,
                                'nickname' => 'Henri-Matisse',
                                'nicknameSlug' => 'henri-matisse',
                                'jobTitle' => null,
                                'website' => null,
                                'signature' => null,
                                'avatar' => [
                                    'xSmall' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_x_small/matisse-avatar.jpg',
                                    'small' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_small/matisse-avatar.jpg',
                                    'medium' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_medium/matisse-avatar.jpg',
                                ],
                                'displayAvatar' => false,
                                'forumPostUpvotesCount' => 1,
                                'forumPostsCount' => 1,
                                'createdAt' => '2020-01-01T10:00:00+01:00',
                                'deleted' => false,
                                'admin' => false,
                            ],
                            'contentHtml' => '<p>Category 4.1 - Topic 1 - Post 1</p>',
                            'upvotesCount' => 1,
                            'createdAt' => '2021-01-10T08:30:00+01:00',
                            'updatedAt' => '2021-01-10T08:30:00+01:00',
                            'deleted' => false,
                            'moderated' => false,
                        ],
                        1 => [
                            '@id' => '/forum_posts/18',
                            '@type' => 'ForumPost',
                            'id' => 18,
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
                                'deleted' => false,
                                'admin' => false,
                            ],
                            'contentHtml' => '<p>Category 4.1 - Topic 1 - Post 2</p>',
                            'upvotesCount' => 0,
                            'createdAt' => (new \DateTime('today'))->setTime(0, 20)->format(\DateTimeInterface::RFC3339),
                            'updatedAt' => (new \DateTime('today'))->setTime(0, 20)->format(\DateTimeInterface::RFC3339),
                            'deleted' => false,
                            'moderated' => false,
                        ],
                    ],
                ],
                1 => [
                    '@id' => '/forum_topics/category-5-1-topic-1-lorem',
                    '@type' => 'ForumTopic',
                    'id' => 8,
                    'category' => [
                        '@id' => '/forum_categories/category-7-1',
                        '@type' => 'ForumCategory',
                        'id' => 17,
                        'title' => 'Category 7.1',
                        'slug' => 'category-7-1',
                    ],
                    'title' => 'Category 5.1 - Topic 1 - Lorem',
                    'slug' => 'category-5-1-topic-1-lorem',
                    'pinned' => false,
                    'lastPost' => [
                        '@id' => '/forum_posts/21',
                        '@type' => 'ForumPost',
                        'id' => 21,
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
                            'deleted' => false,
                            'admin' => false,
                        ],
                        'contentHtml' => '<p>Category 5.1 - Topic 1 - Post 2.1</p>',
                        'upvotesCount' => 0,
                        'createdAt' => (new \DateTime('today'))->setTime(0, 10)->format(\DateTimeInterface::RFC3339),
                        'updatedAt' => (new \DateTime('today'))->setTime(0, 10)->format(\DateTimeInterface::RFC3339),
                        'deleted' => false,
                        'moderated' => false,
                    ],
                    'author' => [
                        '@id' => '/users/9',
                        '@type' => 'User',
                        'id' => 9,
                        'nickname' => 'Henri-Matisse',
                        'nicknameSlug' => 'henri-matisse',
                        'jobTitle' => null,
                        'website' => null,
                        'signature' => null,
                        'avatar' => [
                            'xSmall' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_x_small/matisse-avatar.jpg',
                            'small' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_small/matisse-avatar.jpg',
                            'medium' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_medium/matisse-avatar.jpg',
                        ],
                        'displayAvatar' => false,
                        'forumPostUpvotesCount' => 1,
                        'forumPostsCount' => 1,
                        'createdAt' => '2020-01-01T10:00:00+01:00',
                        'deleted' => false,
                        'admin' => false,
                    ],
                    'posts' => [
                        0 => [
                            '@id' => '/forum_posts/19',
                            '@type' => 'ForumPost',
                            'id' => 19,
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
                                'deleted' => false,
                                'admin' => false,
                            ],
                            'contentHtml' => '<p>Category 5.1 - Topic 1 - Post 1</p>',
                            'upvotesCount' => 0,
                            'createdAt' => '2021-01-10T11:00:00+01:00',
                            'updatedAt' => '2021-01-10T11:00:00+01:00',
                            'deleted' => false,
                            'moderated' => false,
                        ],
                        1 => [
                            '@id' => '/forum_posts/21',
                            '@type' => 'ForumPost',
                            'id' => 21,
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
                                'deleted' => false,
                                'admin' => false,
                            ],
                            'contentHtml' => '<p>Category 5.1 - Topic 1 - Post 2.1</p>',
                            'upvotesCount' => 0,
                            'createdAt' => (new \DateTime('today'))->setTime(0, 10)->format(\DateTimeInterface::RFC3339),
                            'updatedAt' => (new \DateTime('today'))->setTime(0, 10)->format(\DateTimeInterface::RFC3339),
                            'deleted' => false,
                            'moderated' => false,
                        ],
                    ],
                ],
            ],
            'hydra:totalItems' => 6,
            'hydra:view' => [
                '@id' => '/forum_topics/search?q=topic%201&page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/forum_topics/search?q=topic%201&page=1',
                'hydra:last' => '/forum_topics/search?q=topic%201&page=3',
                'hydra:next' => '/forum_topics/search?q=topic%201&page=2',
            ],
            'hydra:search' => [
                '@type' => 'hydra:IriTemplate',
                'hydra:template' => '/forum_topics/search{?category.slug,category.slug[],title,order[lastPost.createdAt],q}',
                'hydra:variableRepresentation' => 'BasicRepresentation',
                'hydra:mapping' => [
                    0 => [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'category.slug',
                        'property' => 'category.slug',
                        'required' => false,
                    ],
                    1 => [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'category.slug[]',
                        'property' => 'category.slug',
                        'required' => false,
                    ],
                    2 => [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'title',
                        'property' => 'title',
                        'required' => false,
                    ],
                    3 => [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'order[lastPost.createdAt]',
                        'property' => 'lastPost.createdAt',
                        'required' => false,
                    ],
                    4 => [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'q',
                        'property' => 'title, posts.content',
                        'required' => false,
                    ],
                ],
            ],
        ]);
    }

    public function testSearchWithQueryOnPostContentWithResults(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/forum_topics/search?q=Post+2.2.1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumTopic',
            '@id' => '/forum_topics',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@type' => 'ForumTopic',
                    'title' => 'Category 1.1 - Topic 2',
                    'lastPost' => [
                        '@type' => 'ForumPost',
                        'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.2.1</p>',
                        'createdAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTimeInterface::RFC3339),
                        'updatedAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTimeInterface::RFC3339),
                    ],
                    'posts' => [
                        [
                            '@type' => 'ForumPost',
                            'author' => [
                                '@type' => 'User',
                                'avatar' => null,
                                'nickname' => 'Vincent-van-Gogh',
                                'nicknameSlug' => 'vincent-van-gogh',
                                'forumPostUpvotesCount' => 0,
                                'forumPostsCount' => 6,
                                'createdAt' => '2020-01-01T10:00:00+01:00',
                                'deleted' => false,
                                'admin' => false,
                            ],
                            'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.2.1</p>',
                            'createdAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTimeInterface::RFC3339),
                            'updatedAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTimeInterface::RFC3339),
                        ],
                    ],
                ],
            ],
            'hydra:totalItems' => 1,
        ]);
    }

    public function testSearchWithQueryOnTopicTitleAndPostContentWithResults(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/forum_topics/search?q=lorem');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumTopic',
            '@id' => '/forum_topics',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/forum_topics/category-1-1-topic-2',
                    '@type' => 'ForumTopic',
                    'id' => 2,
                    'category' => [
                        '@id' => '/forum_categories/category-1-1',
                        '@type' => 'ForumCategory',
                        'id' => 2,
                        'title' => 'Category 1.1',
                        'slug' => 'category-1-1',
                    ],
                    'title' => 'Category 1.1 - Topic 2',
                    'slug' => 'category-1-1-topic-2',
                    'pinned' => false,
                    'lastPost' => [
                        '@id' => '/forum_posts/7',
                        '@type' => 'ForumPost',
                        'id' => 7,
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
                        ],
                        'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.2.1</p>',
                        'upvotesCount' => 0,
                        'createdAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTimeInterface::RFC3339),
                        'updatedAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTimeInterface::RFC3339),
                        'deleted' => false,
                        'moderated' => false,
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
                        'deleted' => false,
                    ],
                    'posts' => [
                        [
                            '@id' => '/forum_posts/4',
                            '@type' => 'ForumPost',
                            'id' => 4,
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
                                'deleted' => false,
                            ],
                            'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2 - Lorem</p>',
                            'upvotesCount' => 0,
                            'createdAt' => '2021-01-10T19:30:00+01:00',
                            'updatedAt' => '2021-01-10T19:30:00+01:00',
                            'deleted' => false,
                            'moderated' => false,
                        ],
                    ],
                ],
                [
                    '@id' => '/forum_topics/category-5-1-topic-1-lorem',
                    '@type' => 'ForumTopic',
                    'id' => 8,
                    'category' => [
                        '@id' => '/forum_categories/category-7-1',
                        '@type' => 'ForumCategory',
                        'id' => 17,
                        'title' => 'Category 7.1',
                        'slug' => 'category-7-1',
                    ],
                    'title' => 'Category 5.1 - Topic 1 - Lorem',
                    'slug' => 'category-5-1-topic-1-lorem',
                    'pinned' => false,
                    'lastPost' => [
                        '@id' => '/forum_posts/21',
                        '@type' => 'ForumPost',
                        'id' => 21,
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
                            'deleted' => false,
                        ],
                        'contentHtml' => '<p>Category 5.1 - Topic 1 - Post 2.1</p>',
                        'upvotesCount' => 0,
                        'createdAt' => (new \DateTime('today'))->setTime(0, 10)->format(\DateTimeInterface::RFC3339),
                        'updatedAt' => (new \DateTime('today'))->setTime(0, 10)->format(\DateTimeInterface::RFC3339),
                        'deleted' => false,
                        'moderated' => false,
                    ],
                    'author' => [
                        '@id' => '/users/9',
                        '@type' => 'User',
                        'id' => 9,
                        'nickname' => 'Henri-Matisse',
                        'nicknameSlug' => 'henri-matisse',
                        'jobTitle' => null,
                        'website' => null,
                        'signature' => null,
                        'avatar' => [
                            'xSmall' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_x_small/matisse-avatar.jpg',
                            'small' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_small/matisse-avatar.jpg',
                            'medium' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_medium/matisse-avatar.jpg',
                        ],
                        'displayAvatar' => false,
                        'forumPostUpvotesCount' => 1,
                        'forumPostsCount' => 1,
                        'createdAt' => '2020-01-01T10:00:00+01:00',
                        'deleted' => false,
                    ],
                    'posts' => [],
                ],
            ],
            'hydra:totalItems' => 3,
            'hydra:view' => [
                '@id' => '/forum_topics/search?q=lorem&page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/forum_topics/search?q=lorem&page=1',
                'hydra:last' => '/forum_topics/search?q=lorem&page=2',
                'hydra:next' => '/forum_topics/search?q=lorem&page=2',
            ],
        ]);
    }

    public function testSearchWithTitleOnTopicWithResults(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/forum_topics/search?title=topic+3');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumTopic',
            '@id' => '/forum_topics',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@type' => 'ForumTopic',
                    'title' => 'Category 1.1 - Topic 3',
                    'lastPost' => [
                        '@type' => 'ForumPost',
                        'contentHtml' => '<p>Category 1.1 - Topic 3 - Post 1</p>',
                        'createdAt' => '2021-01-09T19:00:00+01:00',
                        'updatedAt' => '2021-01-09T19:00:00+01:00',
                    ],
                    'posts' => [
                        [
                            '@type' => 'ForumPost',
                            'author' => [
                                '@type' => 'User',
                                'avatar' => null,
                                'nickname' => 'Vincent-van-Gogh',
                                'nicknameSlug' => 'vincent-van-gogh',
                                'forumPostUpvotesCount' => 0,
                                'forumPostsCount' => 6,
                                'createdAt' => '2020-01-01T10:00:00+01:00',
                                'deleted' => false,
                            ],
                            'contentHtml' => '<p>Category 1.1 - Topic 3 - Post 1</p>',
                            'createdAt' => '2021-01-09T19:00:00+01:00',
                            'updatedAt' => '2021-01-09T19:00:00+01:00',
                        ],
                    ],
                ],
            ],
            'hydra:totalItems' => 1,
        ]);
    }
}
