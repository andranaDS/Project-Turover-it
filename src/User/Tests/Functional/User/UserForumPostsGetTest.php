<?php

namespace App\User\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;

class UserForumPostsGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/users/1/forum_posts');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/users/1/forum_posts');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('GET', '/users/1/forum_posts');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testWithoutPosts(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/users/1/forum_posts');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumPost',
            '@id' => '/users/1/forum_posts',
            '@type' => 'hydra:Collection',
            'hydra:member' => [],
            'hydra:totalItems' => 0,
        ]);
    }

    public function testWithPosts(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/users/6/forum_posts');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumPost',
            '@id' => '/users/6/forum_posts',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@type' => 'ForumPost',
                    'topic' => [
                        '@type' => 'ForumTopic',
                        'category' => [
                            '@type' => 'ForumCategory',
                            'title' => 'Category 4.1',
                            'slug' => 'category-4-1',
                        ],
                        'title' => 'Category 4.1 - Topic 1',
                        'slug' => 'category-4-1-topic-1',
                    ],
                    'parent' => null,
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
                    ],
                    'contentHtml' => '<p>Category 4.1 - Topic 1 - Post 2</p>',
                    'createdAt' => (new \DateTime('today'))->setTime(0, 20)->format(\DateTime::RFC3339),
                ],
                [
                    '@type' => 'ForumPost',
                    'topic' => [
                        '@type' => 'ForumTopic',
                        'category' => [
                            '@type' => 'ForumCategory',
                            'title' => 'Category 1.1',
                            'slug' => 'category-1-1',
                        ],
                        'title' => 'Category 1.1 - Topic 2',
                        'slug' => 'category-1-1-topic-2',
                    ],
                    'parent' => null,
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
                    ],
                    'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 3</p>',
                    'createdAt' => '2021-01-10T20:30:00+01:00',
                ],
            ],
            'hydra:totalItems' => 7,
        ]);
    }

    public function testWithExistingUser(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/users/7/forum_posts');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumPost',
            '@id' => '/users/7/forum_posts',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/forum_posts/7',
                    '@type' => 'ForumPost',
                    'id' => 7,
                    'topic' => [
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
                    ],
                    'parent' => [
                        '@id' => '/forum_posts/6',
                        '@type' => 'ForumPost',
                        'id' => 6,
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
                        'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.2</p>',
                        'upvotesCount' => 0,
                        'createdAt' => '2021-01-10T20:30:00+01:00',
                        'updatedAt' => '2021-01-10T20:30:00+01:00',
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
                    'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.2.1</p>',
                    'upvotesCount' => 0,
                    'createdAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTime::RFC3339),
                    'updatedAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTime::RFC3339),
                    'deleted' => false,
                    'moderated' => false,
                ],
                [
                    '@id' => '/forum_posts/8',
                    '@type' => 'ForumPost',
                    'id' => 8,
                    'topic' => [
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
                    ],
                    'parent' => [
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
                    'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.3</p>',
                    'upvotesCount' => 0,
                    'createdAt' => (new \DateTime('today'))->setTime(0, 25)->format(\DateTime::RFC3339),
                    'updatedAt' => (new \DateTime('today'))->setTime(0, 25)->format(\DateTime::RFC3339),
                    'deleted' => false,
                    'moderated' => false,
                ],
            ],
            'hydra:totalItems' => 6,
            'hydra:view' => [
                '@id' => '/users/7/forum_posts?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/users/7/forum_posts?page=1',
                'hydra:last' => '/users/7/forum_posts?page=3',
                'hydra:next' => '/users/7/forum_posts?page=2',
            ],
        ]);
    }

    public function testWithNonExistentUser(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/users/user-non-existent/forum_posts');

        self::assertResponseStatusCodeSame(404);
    }
}
