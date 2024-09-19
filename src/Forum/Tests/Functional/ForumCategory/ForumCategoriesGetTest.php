<?php

namespace App\Forum\Tests\Functional\ForumCategory;

use App\Tests\Functional\ApiTestCase;

class ForumCategoriesGetTest extends ApiTestCase
{
    public function testWithoutSite(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/forum_categories');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumCategory',
            '@id' => '/forum_categories',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@type' => 'ForumCategory',
                    'children' => [
                        [
                            '@type' => 'ForumCategory',
                            'children' => [],
                            'title' => 'Category 1.1',
                            'slug' => 'category-1-1',
                            'description' => 'Lorem ipsum dolor sit amet',
                            'topicsCount' => 4,
                            'postsCount' => 11,
                            'lastPost' => [
                                '@type' => 'ForumPost',
                                'author' => [
                                    '@type' => 'User',
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
                ],
                [
                    '@type' => 'ForumCategory',
                    'children' => [
                        [
                            '@type' => 'ForumCategory',
                            'children' => [],
                            'title' => 'Category 2.1',
                            'slug' => 'category-2-1',
                            'description' => 'Nullam erat purus, bibendum ac velit ut, commodo tincidunt nisl',
                            'topicsCount' => 1,
                            'postsCount' => 2,
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
                                'topic' => [
                                    '@type' => 'ForumTopic',
                                    'title' => 'Category 2.1 - Topic 1',
                                    'slug' => 'category-2-1-topic-1',
                                ],
                                'createdAt' => '2021-01-04T08:30:00+01:00',
                                'updatedAt' => '2021-01-04T08:30:00+01:00',
                            ],
                        ],
                        [
                            '@type' => 'ForumCategory',
                            'children' => [],
                            'title' => 'Category 2.2',
                            'slug' => 'category-2-2',
                            'description' => 'Phasellus tincidunt, leo et tempus finibus, ex mi feugiat purus, vitae molestie felis orci et velit',
                            'topicsCount' => 0,
                            'postsCount' => 0,
                            'lastPost' => null,
                        ],
                        [
                            '@type' => 'ForumCategory',
                            'children' => [],
                            'title' => 'Category 2.3',
                            'slug' => 'category-2-3',
                            'description' => 'Nullam interdum in elit sit amet eleifend. Integer mollis metus non imperdiet sagittis. Integer vitae felis velit',
                            'topicsCount' => 0,
                            'postsCount' => 0,
                            'lastPost' => null,
                        ],
                    ],
                    'title' => 'Category 2',
                    'slug' => 'category-2',
                    'description' => null,
                    'topicsCount' => 0,
                    'postsCount' => 0,
                    'lastPost' => null,
                ],
            ],
            'hydra:totalItems' => 7,
            'hydra:view' => [
                '@id' => '/forum_categories?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/forum_categories?page=1',
                'hydra:last' => '/forum_categories?page=4',
                'hydra:next' => '/forum_categories?page=2',
            ],
        ]);
    }

    public function testWithLocaleEqualsToDefaultLocale(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('GET', '/forum_categories', [
            'headers' => [
                'Accept-Language' => 'fr',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumCategory',
            '@id' => '/forum_categories',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@type' => 'ForumCategory',
                    'title' => 'Category 1',
                    'slug' => 'category-1',
                    'description' => null,
                    'topicsCount' => 0,
                    'postsCount' => 0,
                    'lastPost' => null,
                ],
                [
                    '@type' => 'ForumCategory',
                    'title' => 'Category 2',
                    'slug' => 'category-2',
                    'description' => null,
                    'topicsCount' => 0,
                    'postsCount' => 0,
                    'lastPost' => null,
                ],
            ],
            'hydra:totalItems' => 7,
            'hydra:view' => [
                '@id' => '/forum_categories?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/forum_categories?page=1',
                'hydra:last' => '/forum_categories?page=4',
                'hydra:next' => '/forum_categories?page=2',
            ],
        ]);
    }

    public function testWithOtherLocale(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('GET', '/forum_categories', [
            'headers' => [
                'Accept-Language' => 'en-gb',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumCategory',
            '@id' => '/forum_categories',
            '@type' => 'hydra:Collection',
            'hydra:member' => [],
            'hydra:totalItems' => 0,
        ]);
    }

    public function testWithPropertiesFilter(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('GET', '/forum_categories?pagination=false&properties[]=slug');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonEquals([
            '@context' => '/contexts/ForumCategory',
            '@id' => '/forum_categories',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/forum_categories/category-1',
                    '@type' => 'ForumCategory',
                    'slug' => 'category-1',
                ],
                [
                    '@id' => '/forum_categories/category-2',
                    '@type' => 'ForumCategory',
                    'slug' => 'category-2',
                ],
                [
                    '@id' => '/forum_categories/category-3',
                    '@type' => 'ForumCategory',
                    'slug' => 'category-3',
                ],
                [
                    '@id' => '/forum_categories/category-4',
                    '@type' => 'ForumCategory',
                    'slug' => 'category-4',
                ],
                [
                    '@id' => '/forum_categories/category-6',
                    '@type' => 'ForumCategory',
                    'slug' => 'category-6',
                ],
                [
                    '@id' => '/forum_categories/category-7',
                    '@type' => 'ForumCategory',
                    'slug' => 'category-7',
                ],
                [
                    '@id' => '/forum_categories/category-8',
                    '@type' => 'ForumCategory',
                    'slug' => 'category-8',
                ],
            ],
            'hydra:totalItems' => 7,
            'hydra:view' => [
                '@id' => '/forum_categories?pagination=false&properties%5B%5D=slug',
                '@type' => 'hydra:PartialCollectionView',
            ],
            'hydra:search' => [
                '@type' => 'hydra:IriTemplate',
                'hydra:template' => '/forum_categories{?properties[]}',
                'hydra:variableRepresentation' => 'BasicRepresentation',
                'hydra:mapping' => [
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'properties[]',
                        'property' => null,
                        'required' => false,
                    ],
                ],
            ],
        ]);
    }
}
