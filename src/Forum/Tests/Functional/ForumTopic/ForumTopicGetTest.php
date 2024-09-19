<?php

namespace App\Forum\Tests\Functional\ForumTopic;

use App\Tests\Functional\ApiTestCase;

class ForumTopicGetTest extends ApiTestCase
{
    public function testWithReplies(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/forum_topics/category-1-1-topic-2');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumTopic',
            '@type' => 'ForumTopic',
            'category' => [
                '@type' => 'ForumCategory',
                'title' => 'Category 1.1',
                'slug' => 'category-1-1',
            ],
            'title' => 'Category 1.1 - Topic 2',
            'slug' => 'category-1-1-topic-2',
            'metaTitle' => 'Forum - Topic 2 // Meta title',
            'metaDescription' => 'Blog - Topic 2 // Meta description',
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
                ],
                'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.2.1</p>',
                'createdAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTime::RFC3339),
                'updatedAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTime::RFC3339),
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
            'initialPost' => [
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
                    'forumRank' => 0,
                    'admin' => false,
                ],
                'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 1</p>',
                'createdAt' => '2021-01-10T19:00:00+01:00',
                'updatedAt' => '2021-01-10T19:00:00+01:00',
            ],
        ]);
    }

    public function testWithoutReplies(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/forum_topics/category-1-1-topic-3');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumTopic',
            '@type' => 'ForumTopic',
            'category' => [
                '@type' => 'ForumCategory',
                'title' => 'Category 1.1',
                'slug' => 'category-1-1',
            ],
            'title' => 'Category 1.1 - Topic 3',
            'slug' => 'category-1-1-topic-3',
            'pinned' => true,
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
                ],
                'contentHtml' => '<p>Category 1.1 - Topic 3 - Post 1</p>',
                'createdAt' => '2021-01-09T19:00:00+01:00',
                'updatedAt' => '2021-01-09T19:00:00+01:00',
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
                'forumRank' => 0,
                'admin' => false,
            ],
            'initialPost' => [
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
                'contentHtml' => '<p>Category 1.1 - Topic 3 - Post 1</p>',
                'createdAt' => '2021-01-09T19:00:00+01:00',
                'updatedAt' => '2021-01-09T19:00:00+01:00',
            ],
        ]);
    }

    public function testWithModeratedAndHidden(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/forum_topics/category-3-1-topic-1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumTopic',
            '@type' => 'ForumTopic',
            'category' => [
                '@type' => 'ForumCategory',
                'title' => 'Category 3.1',
                'slug' => 'category-3-1',
            ],
            'title' => 'Category 3.1 - Topic 1',
            'slug' => 'category-3-1-topic-1',
            'pinned' => false,
            'lastPost' => [
                '@type' => 'ForumPost',
                'author' => [
                    '@type' => 'User',
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
                'contentHtml' => null,
                'upvotesCount' => 0,
                'createdAt' => '2021-01-04T08:25:00+01:00',
                'updatedAt' => '2021-01-04T08:25:00+01:00',
                'deleted' => false,
                'moderated' => true,
            ],
            'author' => [
                '@type' => 'User',
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
                'admin' => false,
            ],
            'initialPost' => [
                '@type' => 'ForumPost',
                'author' => [
                    '@type' => 'User',
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
                    'admin' => false,
                ],
                'contentHtml' => null,
                'upvotesCount' => 0,
                'createdAt' => '2021-01-04T08:25:00+01:00',
                'updatedAt' => '2021-01-04T08:25:00+01:00',
                'deleted' => false,
                'moderated' => true,
            ],
        ]);
    }

    public function testWithDeleted(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/forum_topics/category-5-1-topic-1-lorem');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumTopic',
            '@type' => 'ForumTopic',
            'category' => [
                '@type' => 'ForumCategory',
                'title' => 'Category 7.1',
                'slug' => 'category-7-1',
            ],
            'title' => 'Category 5.1 - Topic 1 - Lorem',
            'slug' => 'category-5-1-topic-1-lorem',
            'pinned' => false,
            'lastPost' => [
                '@type' => 'ForumPost',
                'author' => [
                    '@type' => 'User',
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
                'createdAt' => (new \DateTime('today'))->setTime(0, 10)->format(\DateTime::RFC3339),
                'updatedAt' => (new \DateTime('today'))->setTime(0, 10)->format(\DateTime::RFC3339),
                'deleted' => false,
                'moderated' => false,
            ],
            'author' => [
                '@type' => 'User',
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
                'forumRank' => 0,
                'admin' => false,
            ],
            'initialPost' => [
                '@type' => 'ForumPost',
                'author' => [
                    '@type' => 'User',
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
        ]);
    }
}
