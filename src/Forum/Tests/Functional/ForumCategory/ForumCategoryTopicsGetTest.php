<?php

namespace App\Forum\Tests\Functional\ForumCategory;

use App\Tests\Functional\ApiTestCase;

class ForumCategoryTopicsGetTest extends ApiTestCase
{
    public function testWithExistingCategory(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/forum_categories/category-1-1/topics');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumTopic',
            '@id' => '/forum_categories/category-1-1/topics',
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
                            'deleted' => false,
                            'admin' => true,
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
                        'admin' => true,
                    ],
                ],
            ],
            'hydra:totalItems' => 4,
            'hydra:view' => [
                '@id' => '/forum_categories/category-1-1/topics?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/forum_categories/category-1-1/topics?page=1',
                'hydra:last' => '/forum_categories/category-1-1/topics?page=2',
                'hydra:next' => '/forum_categories/category-1-1/topics?page=2',
            ],
        ]);
    }

    public function testWithNonExistentCategory(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/forum_categories/forum-category-non-existent/topics');

        self::assertResponseStatusCodeSame(404);
    }
}
