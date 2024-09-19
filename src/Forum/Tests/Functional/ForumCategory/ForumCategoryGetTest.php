<?php

namespace App\Forum\Tests\Functional\ForumCategory;

use App\Tests\Functional\ApiTestCase;

class ForumCategoryGetTest extends ApiTestCase
{
    public function testWithChildren(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/forum_categories/category-1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumCategory',
            '@type' => 'ForumCategory',
            'children' => [
                [
                    '@type' => 'ForumCategory',
                    'children' => [],
                    'title' => 'Category 1.1',
                    'slug' => 'category-1-1',
                    'description' => 'Lorem ipsum dolor sit amet',
                    'metaTitle' => 'Forum - Category 1.1 // Meta title',
                    'metaDescription' => 'Forum - Category 1.1 // Meta description',
                    'topicsCount' => 4,
                    'postsCount' => 11,
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
                        'topic' => [
                            '@type' => 'ForumTopic',
                            'title' => 'Category 1.1 - Topic 2',
                            'slug' => 'category-1-1-topic-2',
                        ],
                        'createdAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTime::RFC3339),
                        'updatedAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTime::RFC3339),
                    ],
                ],
                [
                    '@type' => 'ForumCategory',
                    'children' => [],
                    'title' => 'Category 1.2',
                    'slug' => 'category-1-2',
                    'description' => 'Curabitur eros eros, maximus lobortis sollicitudin eget, gravida nec velit',
                    'topicsCount' => 1,
                    'postsCount' => 1,
                    'lastPost' => [
                        '@type' => 'ForumPost',
                        'author' => [
                            '@type' => 'User',
                            'jobTitle' => 'Peintre',
                            'website' => 'https://fr.wikipedia.org/wiki/Claude_Monet',
                            'signature' => 'Claude Monet.',
                            'avatar' => [
                                'xSmall' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_x_small/monet-avatar.jpg',
                                'small' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_small/monet-avatar.jpg',
                                'medium' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_medium/monet-avatar.jpg',
                            ],
                            'displayAvatar' => true,
                            'nickname' => 'Claude-Monet',
                            'nicknameSlug' => 'claude-monet',
                            'forumPostUpvotesCount' => 2,
                            'forumPostsCount' => 7,
                            'createdAt' => '2020-01-01T10:00:00+01:00',
                            'deleted' => false,
                        ],
                        'topic' => [
                            '@type' => 'ForumTopic',
                            'title' => 'Category 1.2 - Topic 1',
                            'slug' => 'category-1-2-topic-1',
                        ],
                        'createdAt' => '2021-01-03T12:00:00+01:00',
                        'updatedAt' => '2021-01-03T12:00:00+01:00',
                    ],
                ],
            ],
            'title' => 'Category 1',
            'slug' => 'category-1',
            'description' => null,
            'topicsCount' => 0,
            'postsCount' => 0,
            'lastPost' => null,
        ]);
    }

    public function testWithoutChildren(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/forum_categories/category-1-1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumCategory',
            '@type' => 'ForumCategory',
            'children' => [],
            'title' => 'Category 1.1',
            'slug' => 'category-1-1',
            'metaTitle' => 'Forum - Category 1.1 // Meta title',
            'metaDescription' => 'Forum - Category 1.1 // Meta description',
            'description' => 'Lorem ipsum dolor sit amet',
            'topicsCount' => 4,
            'postsCount' => 11,
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
                'topic' => [
                    '@type' => 'ForumTopic',
                    'title' => 'Category 1.1 - Topic 2',
                    'slug' => 'category-1-1-topic-2',
                ],
                'createdAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTime::RFC3339),
                'updatedAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTime::RFC3339),
            ],
        ]);
    }
}
