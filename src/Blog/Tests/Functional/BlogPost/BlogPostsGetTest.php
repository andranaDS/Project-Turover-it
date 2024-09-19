<?php

namespace App\Blog\Tests\Functional\BlogPost;

use App\Tests\Functional\ApiTestCase;

class BlogPostsGetTest extends ApiTestCase
{
    public function testWithoutLocale(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_posts');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/BlogPost',
            '@id' => '/blog_posts',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/blog_posts/blog-category-1-post-1',
                    '@type' => 'BlogPost',
                    'id' => 1,
                    'title' => 'Blog - Category 1 - Post 1',
                    'slug' => 'blog-category-1-post-1',
                    'excerpt' => 'Blog - Category 1 - Post 1 // Excerpt',
                    'category' => [
                        '@id' => '/blog_categories/blog-category-1',
                        '@type' => 'BlogCategory',
                        'id' => 1,
                        'name' => 'Blog - Category 1',
                        'slug' => 'blog-category-1',
                    ],
                    'tags' => [
                        [
                            '@id' => '/blog_tags/blog-tag-1',
                            '@type' => 'BlogTag',
                            'id' => 1,
                            'name' => 'Blog - Tag 1',
                            'slug' => 'blog-tag-1',
                        ],
                        [
                            '@id' => '/blog_tags/blog-tag-2',
                            '@type' => 'BlogTag',
                            'id' => 2,
                            'name' => 'Blog - Tag 2',
                            'slug' => 'blog-tag-2',
                        ],
                        [
                            '@id' => '/blog_tags/blog-tag-3',
                            '@type' => 'BlogTag',
                            'id' => 3,
                            'name' => 'Blog - Tag 3',
                            'slug' => 'blog-tag-3',
                        ],
                    ],
                    'readingTimeMinutes' => 1,
                    'image' => [
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_medium/cat1art1-image.jpg',
                        'large' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_large/cat1art1-image.jpg',
                    ],
                    'imageAlt' => null,
                    'publishedAt' => '2021-01-01T20:00:00+01:00',
                    'updatedAt' => '2021-01-01T22:00:00+01:00',
                    'modified' => true,
                ],
                [
                    '@id' => '/blog_posts/blog-category-1-post-4',
                    '@type' => 'BlogPost',
                    'id' => 4,
                    'title' => 'Blog - Category 1 - Post 4',
                    'slug' => 'blog-category-1-post-4',
                    'excerpt' => 'Blog - Category 1 - Post 4 // Excerpt',
                    'category' => [
                        '@id' => '/blog_categories/blog-category-1',
                        '@type' => 'BlogCategory',
                        'id' => 1,
                        'name' => 'Blog - Category 1',
                        'slug' => 'blog-category-1',
                    ],
                    'tags' => [
                        [
                            '@id' => '/blog_tags/blog-tag-1',
                            '@type' => 'BlogTag',
                            'id' => 1,
                            'name' => 'Blog - Tag 1',
                            'slug' => 'blog-tag-1',
                        ],
                    ],
                    'readingTimeMinutes' => 1,
                    'image' => [
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_medium/cat1art4-image.jpg',
                        'large' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_large/cat1art4-image.jpg',
                    ],
                    'imageAlt' => 'Blog - Category 1 - Post 4 // Image alt',
                    'publishedAt' => '2021-01-01T13:00:00+01:00',
                    'updatedAt' => '2021-01-01T13:00:00+01:00',
                    'modified' => true,
                ],
            ],
            'hydra:totalItems' => 5,
            'hydra:view' => [
                '@id' => '/blog_posts?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/blog_posts?page=1',
                'hydra:last' => '/blog_posts?page=3',
                'hydra:next' => '/blog_posts?page=2',
            ],
        ]);
    }

    public function testWithLocale(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_posts', [
            'headers' => [
                'Accept-Language' => 'fr-be',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/BlogPost',
            '@id' => '/blog_posts',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/blog_posts/blog-category-1-post-1',
                    '@type' => 'BlogPost',
                    'id' => 1,
                    'title' => 'Blog - Category 1 - Post 1',
                    'slug' => 'blog-category-1-post-1',
                    'excerpt' => 'Blog - Category 1 - Post 1 // Excerpt',
                    'category' => [
                        '@id' => '/blog_categories/blog-category-1',
                        '@type' => 'BlogCategory',
                        'id' => 1,
                        'name' => 'Blog - Category 1',
                        'slug' => 'blog-category-1',
                    ],
                    'tags' => [
                        [
                            '@id' => '/blog_tags/blog-tag-1',
                            '@type' => 'BlogTag',
                            'id' => 1,
                            'name' => 'Blog - Tag 1',
                            'slug' => 'blog-tag-1',
                        ],
                        [
                            '@id' => '/blog_tags/blog-tag-2',
                            '@type' => 'BlogTag',
                            'id' => 2,
                            'name' => 'Blog - Tag 2',
                            'slug' => 'blog-tag-2',
                        ],
                        [
                            '@id' => '/blog_tags/blog-tag-3',
                            '@type' => 'BlogTag',
                            'id' => 3,
                            'name' => 'Blog - Tag 3',
                            'slug' => 'blog-tag-3',
                        ],
                    ],
                    'readingTimeMinutes' => 1,
                    'image' => [
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_medium/cat1art1-image.jpg',
                        'large' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_large/cat1art1-image.jpg',
                    ],
                    'imageAlt' => null,
                    'publishedAt' => '2021-01-01T20:00:00+01:00',
                    'updatedAt' => '2021-01-01T22:00:00+01:00',
                    'modified' => true,
                ],
                [
                    '@id' => '/blog_posts/blog-category-1-post-4',
                    '@type' => 'BlogPost',
                    'id' => 4,
                    'title' => 'Blog - Category 1 - Post 4',
                    'slug' => 'blog-category-1-post-4',
                    'excerpt' => 'Blog - Category 1 - Post 4 // Excerpt',
                    'category' => [
                        '@id' => '/blog_categories/blog-category-1',
                        '@type' => 'BlogCategory',
                        'id' => 1,
                        'name' => 'Blog - Category 1',
                        'slug' => 'blog-category-1',
                    ],
                    'tags' => [
                        [
                            '@id' => '/blog_tags/blog-tag-1',
                            '@type' => 'BlogTag',
                            'id' => 1,
                            'name' => 'Blog - Tag 1',
                            'slug' => 'blog-tag-1',
                        ],
                    ],
                    'readingTimeMinutes' => 1,
                    'image' => [
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_medium/cat1art4-image.jpg',
                        'large' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_large/cat1art4-image.jpg',
                    ],
                    'imageAlt' => 'Blog - Category 1 - Post 4 // Image alt',
                    'publishedAt' => '2021-01-01T13:00:00+01:00',
                    'updatedAt' => '2021-01-01T13:00:00+01:00',
                    'modified' => true,
                ],
            ],
            'hydra:totalItems' => 6,
            'hydra:view' => [
                '@id' => '/blog_posts?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/blog_posts?page=1',
                'hydra:last' => '/blog_posts?page=3',
                'hydra:next' => '/blog_posts?page=2',
            ],
        ]);
    }

    public function testWithTagFilter(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_posts?tags.slug=blog-tag-2');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/BlogPost',
            '@id' => '/blog_posts',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/blog_posts/blog-category-1-post-1',
                    '@type' => 'BlogPost',
                    'id' => 1,
                    'title' => 'Blog - Category 1 - Post 1',
                    'slug' => 'blog-category-1-post-1',
                    'excerpt' => 'Blog - Category 1 - Post 1 // Excerpt',
                    'category' => [
                        '@id' => '/blog_categories/blog-category-1',
                        '@type' => 'BlogCategory',
                        'id' => 1,
                        'name' => 'Blog - Category 1',
                        'slug' => 'blog-category-1',
                    ],
                    'tags' => [
                        [
                            '@id' => '/blog_tags/blog-tag-1',
                            '@type' => 'BlogTag',
                            'id' => 1,
                            'name' => 'Blog - Tag 1',
                            'slug' => 'blog-tag-1',
                        ],
                        [
                            '@id' => '/blog_tags/blog-tag-2',
                            '@type' => 'BlogTag',
                            'id' => 2,
                            'name' => 'Blog - Tag 2',
                            'slug' => 'blog-tag-2',
                        ],
                        [
                            '@id' => '/blog_tags/blog-tag-3',
                            '@type' => 'BlogTag',
                            'id' => 3,
                            'name' => 'Blog - Tag 3',
                            'slug' => 'blog-tag-3',
                        ],
                    ],
                    'readingTimeMinutes' => 1,
                    'image' => [
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_medium/cat1art1-image.jpg',
                        'large' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_large/cat1art1-image.jpg',
                    ],
                    'imageAlt' => null,
                    'publishedAt' => '2021-01-01T20:00:00+01:00',
                    'updatedAt' => '2021-01-01T22:00:00+01:00',
                    'modified' => true,
                ],
                [
                    '@id' => '/blog_posts/blog-category-1-post-3-lorem',
                    '@type' => 'BlogPost',
                    'id' => 3,
                    'title' => 'Blog - Category 1 - Post 3 - Lorem',
                    'slug' => 'blog-category-1-post-3-lorem',
                    'excerpt' => 'Blog - Category 1 - Post 3 // Excerpt',
                    'category' => [
                        '@id' => '/blog_categories/blog-category-1',
                        '@type' => 'BlogCategory',
                        'id' => 1,
                        'name' => 'Blog - Category 1',
                        'slug' => 'blog-category-1',
                    ],
                    'tags' => [
                        [
                            '@id' => '/blog_tags/blog-tag-1',
                            '@type' => 'BlogTag',
                            'id' => 1,
                            'name' => 'Blog - Tag 1',
                            'slug' => 'blog-tag-1',
                        ],
                        1 => [
                            '@id' => '/blog_tags/blog-tag-2',
                            '@type' => 'BlogTag',
                            'id' => 2,
                            'name' => 'Blog - Tag 2',
                            'slug' => 'blog-tag-2',
                        ],
                    ],
                    'readingTimeMinutes' => 1,
                    'image' => [
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_medium/cat1art3-image.jpg',
                        'large' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_large/cat1art3-image.jpg',
                    ],
                    'imageAlt' => 'Blog - Category 1 - Post 3 // Image alt',
                    'publishedAt' => '2021-01-01T12:00:00+01:00',
                    'updatedAt' => '2021-01-01T12:00:00+01:00',
                    'modified' => true,
                ],
            ],
            'hydra:totalItems' => 3,
            'hydra:view' => [
                '@id' => '/blog_posts?tags.slug=blog-tag-2&page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/blog_posts?tags.slug=blog-tag-2&page=1',
                'hydra:last' => '/blog_posts?tags.slug=blog-tag-2&page=2',
                'hydra:next' => '/blog_posts?tags.slug=blog-tag-2&page=2',
            ],
        ]);
    }

    public function testWithTagsFilter(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_posts?tags.slug[]=blog-tag-2&tags.slug[]=blog-tag-4');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/BlogPost',
            '@id' => '/blog_posts',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/blog_posts/blog-category-1-post-1',
                    '@type' => 'BlogPost',
                    'id' => 1,
                    'title' => 'Blog - Category 1 - Post 1',
                    'slug' => 'blog-category-1-post-1',
                    'excerpt' => 'Blog - Category 1 - Post 1 // Excerpt',
                    'category' => [
                        '@id' => '/blog_categories/blog-category-1',
                        '@type' => 'BlogCategory',
                        'id' => 1,
                        'name' => 'Blog - Category 1',
                        'slug' => 'blog-category-1',
                    ],
                    'tags' => [
                        [
                            '@id' => '/blog_tags/blog-tag-1',
                            '@type' => 'BlogTag',
                            'id' => 1,
                            'name' => 'Blog - Tag 1',
                            'slug' => 'blog-tag-1',
                        ],
                        [
                            '@id' => '/blog_tags/blog-tag-2',
                            '@type' => 'BlogTag',
                            'id' => 2,
                            'name' => 'Blog - Tag 2',
                            'slug' => 'blog-tag-2',
                        ],
                        [
                            '@id' => '/blog_tags/blog-tag-3',
                            '@type' => 'BlogTag',
                            'id' => 3,
                            'name' => 'Blog - Tag 3',
                            'slug' => 'blog-tag-3',
                        ],
                    ],
                    'readingTimeMinutes' => 1,
                    'image' => [
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_medium/cat1art1-image.jpg',
                        'large' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_large/cat1art1-image.jpg',
                    ],
                    'imageAlt' => null,
                    'publishedAt' => '2021-01-01T20:00:00+01:00',
                    'updatedAt' => '2021-01-01T22:00:00+01:00',
                    'modified' => true,
                ],
                [
                    '@id' => '/blog_posts/blog-category-1-post-3-lorem',
                    '@type' => 'BlogPost',
                    'id' => 3,
                    'title' => 'Blog - Category 1 - Post 3 - Lorem',
                    'slug' => 'blog-category-1-post-3-lorem',
                    'excerpt' => 'Blog - Category 1 - Post 3 // Excerpt',
                    'category' => [
                        '@id' => '/blog_categories/blog-category-1',
                        '@type' => 'BlogCategory',
                        'id' => 1,
                        'name' => 'Blog - Category 1',
                        'slug' => 'blog-category-1',
                    ],
                    'tags' => [
                        [
                            '@id' => '/blog_tags/blog-tag-1',
                            '@type' => 'BlogTag',
                            'id' => 1,
                            'name' => 'Blog - Tag 1',
                            'slug' => 'blog-tag-1',
                        ],
                        [
                            '@id' => '/blog_tags/blog-tag-2',
                            '@type' => 'BlogTag',
                            'id' => 2,
                            'name' => 'Blog - Tag 2',
                            'slug' => 'blog-tag-2',
                        ],
                    ],
                    'readingTimeMinutes' => 1,
                    'image' => [
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_medium/cat1art3-image.jpg',
                        'large' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_large/cat1art3-image.jpg',
                    ],
                    'imageAlt' => 'Blog - Category 1 - Post 3 // Image alt',
                    'publishedAt' => '2021-01-01T12:00:00+01:00',
                    'updatedAt' => '2021-01-01T12:00:00+01:00',
                    'modified' => true,
                ],
            ],
            'hydra:totalItems' => 3,
            'hydra:view' => [
                '@id' => '/blog_posts?tags.slug%5B%5D=blog-tag-2&tags.slug%5B%5D=blog-tag-4&page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/blog_posts?tags.slug%5B%5D=blog-tag-2&tags.slug%5B%5D=blog-tag-4&page=1',
                'hydra:last' => '/blog_posts?tags.slug%5B%5D=blog-tag-2&tags.slug%5B%5D=blog-tag-4&page=2',
                'hydra:next' => '/blog_posts?tags.slug%5B%5D=blog-tag-2&tags.slug%5B%5D=blog-tag-4&page=2',
            ],
        ]);
    }

    public function testWithCategoryFilter(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_posts?category.slug=blog-category-1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/BlogPost',
            '@id' => '/blog_posts',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/blog_posts/blog-category-1-post-1',
                    '@type' => 'BlogPost',
                    'id' => 1,
                    'title' => 'Blog - Category 1 - Post 1',
                    'slug' => 'blog-category-1-post-1',
                    'excerpt' => 'Blog - Category 1 - Post 1 // Excerpt',
                    'category' => [
                        '@id' => '/blog_categories/blog-category-1',
                        '@type' => 'BlogCategory',
                        'id' => 1,
                        'name' => 'Blog - Category 1',
                        'slug' => 'blog-category-1',
                    ],
                    'tags' => [
                        [
                            '@id' => '/blog_tags/blog-tag-1',
                            '@type' => 'BlogTag',
                            'id' => 1,
                            'name' => 'Blog - Tag 1',
                            'slug' => 'blog-tag-1',
                        ],
                        [
                            '@id' => '/blog_tags/blog-tag-2',
                            '@type' => 'BlogTag',
                            'id' => 2,
                            'name' => 'Blog - Tag 2',
                            'slug' => 'blog-tag-2',
                        ],
                        [
                            '@id' => '/blog_tags/blog-tag-3',
                            '@type' => 'BlogTag',
                            'id' => 3,
                            'name' => 'Blog - Tag 3',
                            'slug' => 'blog-tag-3',
                        ],
                    ],
                    'readingTimeMinutes' => 1,
                    'image' => [
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_medium/cat1art1-image.jpg',
                        'large' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_large/cat1art1-image.jpg',
                    ],
                    'imageAlt' => null,
                    'publishedAt' => '2021-01-01T20:00:00+01:00',
                    'updatedAt' => '2021-01-01T22:00:00+01:00',
                    'modified' => true,
                ],
                [
                    '@id' => '/blog_posts/blog-category-1-post-4',
                    '@type' => 'BlogPost',
                    'id' => 4,
                    'title' => 'Blog - Category 1 - Post 4',
                    'slug' => 'blog-category-1-post-4',
                    'excerpt' => 'Blog - Category 1 - Post 4 // Excerpt',
                    'category' => [
                        '@id' => '/blog_categories/blog-category-1',
                        '@type' => 'BlogCategory',
                        'id' => 1,
                        'name' => 'Blog - Category 1',
                        'slug' => 'blog-category-1',
                    ],
                    'tags' => [
                        [
                            '@id' => '/blog_tags/blog-tag-1',
                            '@type' => 'BlogTag',
                            'id' => 1,
                            'name' => 'Blog - Tag 1',
                            'slug' => 'blog-tag-1',
                        ],
                    ],
                    'readingTimeMinutes' => 1,
                    'image' => [
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_medium/cat1art4-image.jpg',
                        'large' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_large/cat1art4-image.jpg',
                    ],
                    'imageAlt' => 'Blog - Category 1 - Post 4 // Image alt',
                    'publishedAt' => '2021-01-01T13:00:00+01:00',
                    'updatedAt' => '2021-01-01T13:00:00+01:00',
                    'modified' => true,
                ],
            ],
            'hydra:totalItems' => 4,
            'hydra:view' => [
                '@id' => '/blog_posts?category.slug=blog-category-1&page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/blog_posts?category.slug=blog-category-1&page=1',
                'hydra:last' => '/blog_posts?category.slug=blog-category-1&page=2',
                'hydra:next' => '/blog_posts?category.slug=blog-category-1&page=2',
            ],
        ]);
    }

    public function testWithCategoriesFilter(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_posts?category.slug[]=blog-category-1&category.slug[]=blog-category-2');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/BlogPost',
            '@id' => '/blog_posts',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/blog_posts/blog-category-1-post-1',
                    '@type' => 'BlogPost',
                    'id' => 1,
                    'title' => 'Blog - Category 1 - Post 1',
                    'slug' => 'blog-category-1-post-1',
                    'excerpt' => 'Blog - Category 1 - Post 1 // Excerpt',
                    'category' => [
                        '@id' => '/blog_categories/blog-category-1',
                        '@type' => 'BlogCategory',
                        'id' => 1,
                        'name' => 'Blog - Category 1',
                        'slug' => 'blog-category-1',
                    ],
                    'tags' => [
                        [
                            '@id' => '/blog_tags/blog-tag-1',
                            '@type' => 'BlogTag',
                            'id' => 1,
                            'name' => 'Blog - Tag 1',
                            'slug' => 'blog-tag-1',
                        ],
                        [
                            '@id' => '/blog_tags/blog-tag-2',
                            '@type' => 'BlogTag',
                            'id' => 2,
                            'name' => 'Blog - Tag 2',
                            'slug' => 'blog-tag-2',
                        ],
                        [
                            '@id' => '/blog_tags/blog-tag-3',
                            '@type' => 'BlogTag',
                            'id' => 3,
                            'name' => 'Blog - Tag 3',
                            'slug' => 'blog-tag-3',
                        ],
                    ],
                    'readingTimeMinutes' => 1,
                    'image' => [
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_medium/cat1art1-image.jpg',
                        'large' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_large/cat1art1-image.jpg',
                    ],
                    'imageAlt' => null,
                    'publishedAt' => '2021-01-01T20:00:00+01:00',
                    'updatedAt' => '2021-01-01T22:00:00+01:00',
                    'modified' => true,
                ],
                [
                    '@id' => '/blog_posts/blog-category-1-post-4',
                    '@type' => 'BlogPost',
                    'id' => 4,
                    'title' => 'Blog - Category 1 - Post 4',
                    'slug' => 'blog-category-1-post-4',
                    'excerpt' => 'Blog - Category 1 - Post 4 // Excerpt',
                    'category' => [
                        '@id' => '/blog_categories/blog-category-1',
                        '@type' => 'BlogCategory',
                        'id' => 1,
                        'name' => 'Blog - Category 1',
                        'slug' => 'blog-category-1',
                    ],
                    'tags' => [
                        [
                            '@id' => '/blog_tags/blog-tag-1',
                            '@type' => 'BlogTag',
                            'id' => 1,
                            'name' => 'Blog - Tag 1',
                            'slug' => 'blog-tag-1',
                        ],
                    ],
                    'readingTimeMinutes' => 1,
                    'image' => [
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_medium/cat1art4-image.jpg',
                        'large' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_large/cat1art4-image.jpg',
                    ],
                    'imageAlt' => 'Blog - Category 1 - Post 4 // Image alt',
                    'publishedAt' => '2021-01-01T13:00:00+01:00',
                    'updatedAt' => '2021-01-01T13:00:00+01:00',
                    'modified' => true,
                ],
            ],
            'hydra:totalItems' => 5,
            'hydra:view' => [
                '@id' => '/blog_posts?category.slug%5B%5D=blog-category-1&category.slug%5B%5D=blog-category-2&page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/blog_posts?category.slug%5B%5D=blog-category-1&category.slug%5B%5D=blog-category-2&page=1',
                'hydra:last' => '/blog_posts?category.slug%5B%5D=blog-category-1&category.slug%5B%5D=blog-category-2&page=3',
                'hydra:next' => '/blog_posts?category.slug%5B%5D=blog-category-1&category.slug%5B%5D=blog-category-2&page=2',
            ],
        ]);
    }

    public function testWithQueryFilter(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_posts?q=lorem');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/BlogPost',
            '@id' => '/blog_posts',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/blog_posts/blog-category-1-post-3-lorem',
                    '@type' => 'BlogPost',
                    'id' => 3,
                    'title' => 'Blog - Category 1 - Post 3 - Lorem',
                    'slug' => 'blog-category-1-post-3-lorem',
                    'excerpt' => 'Blog - Category 1 - Post 3 // Excerpt',
                    'category' => [
                        '@id' => '/blog_categories/blog-category-1',
                        '@type' => 'BlogCategory',
                        'id' => 1,
                        'name' => 'Blog - Category 1',
                        'slug' => 'blog-category-1',
                    ],
                    'tags' => [
                        [
                            '@id' => '/blog_tags/blog-tag-1',
                            '@type' => 'BlogTag',
                            'id' => 1,
                            'name' => 'Blog - Tag 1',
                            'slug' => 'blog-tag-1',
                        ],
                        [
                            '@id' => '/blog_tags/blog-tag-2',
                            '@type' => 'BlogTag',
                            'id' => 2,
                            'name' => 'Blog - Tag 2',
                            'slug' => 'blog-tag-2',
                        ],
                    ],
                    'readingTimeMinutes' => 1,
                    'image' => [
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_medium/cat1art3-image.jpg',
                        'large' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_large/cat1art3-image.jpg',
                    ],
                    'imageAlt' => 'Blog - Category 1 - Post 3 // Image alt',
                    'publishedAt' => '2021-01-01T12:00:00+01:00',
                    'updatedAt' => '2021-01-01T12:00:00+01:00',
                    'modified' => true,
                ],
                [
                    '@id' => '/blog_posts/blog-category-2-post-1',
                    '@type' => 'BlogPost',
                    'id' => 5,
                    'title' => 'Blog - Category 2 - Post 1',
                    'slug' => 'blog-category-2-post-1',
                    'excerpt' => 'Blog - Category 1 - Post 1 // Excerpt',
                    'category' => [
                        '@id' => '/blog_categories/blog-category-2',
                        '@type' => 'BlogCategory',
                        'id' => 2,
                        'name' => 'Blog - Category 2',
                        'slug' => 'blog-category-2',
                    ],
                    'tags' => [
                        [
                            '@id' => '/blog_tags/blog-tag-1',
                            '@type' => 'BlogTag',
                            'id' => 1,
                            'name' => 'Blog - Tag 1',
                            'slug' => 'blog-tag-1',
                        ],
                    ],
                    'readingTimeMinutes' => 1,
                    'image' => [
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_medium/cat2art1-image.jpg',
                        'large' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_large/cat2art1-image.jpg',
                    ],
                    'imageAlt' => 'Blog - Category 2 - Post 1 // Image alt',
                    'publishedAt' => '2021-01-01T12:00:00+01:00',
                    'updatedAt' => '2021-01-01T12:00:00+01:00',
                    'modified' => true,
                ],
            ],
            'hydra:totalItems' => 2,
            'hydra:view' => [
                '@id' => '/blog_posts?q=lorem',
                '@type' => 'hydra:PartialCollectionView',
            ],
        ]);
    }

    public function testWithPropertiesFilter(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_posts?pagination=false&properties[]=slug&properties[category][]=slug');
        self::assertJsonEquals([
            '@context' => '/contexts/BlogPost',
            '@id' => '/blog_posts',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/blog_posts/blog-category-1-post-1',
                    '@type' => 'BlogPost',
                    'slug' => 'blog-category-1-post-1',
                    'category' => [
                        '@id' => '/blog_categories/blog-category-1',
                        '@type' => 'BlogCategory',
                        'slug' => 'blog-category-1',
                    ],
                ],
                [
                    '@id' => '/blog_posts/blog-category-1-post-4',
                    '@type' => 'BlogPost',
                    'slug' => 'blog-category-1-post-4',
                    'category' => [
                        '@id' => '/blog_categories/blog-category-1',
                        '@type' => 'BlogCategory',
                        'slug' => 'blog-category-1',
                    ],
                ],
                [
                    '@id' => '/blog_posts/blog-category-1-post-3-lorem',
                    '@type' => 'BlogPost',
                    'slug' => 'blog-category-1-post-3-lorem',
                    'category' => [
                        '@id' => '/blog_categories/blog-category-1',
                        '@type' => 'BlogCategory',
                        'slug' => 'blog-category-1',
                    ],
                ],
                [
                    '@id' => '/blog_posts/blog-category-2-post-1',
                    '@type' => 'BlogPost',
                    'slug' => 'blog-category-2-post-1',
                    'category' => [
                        '@id' => '/blog_categories/blog-category-2',
                        '@type' => 'BlogCategory',
                        'slug' => 'blog-category-2',
                    ],
                ],
                [
                    '@id' => '/blog_posts/blog-category-1-post-2',
                    '@type' => 'BlogPost',
                    'slug' => 'blog-category-1-post-2',
                    'category' => [
                        '@id' => '/blog_categories/blog-category-1',
                        '@type' => 'BlogCategory',
                        'slug' => 'blog-category-1',
                    ],
                ],
            ],
            'hydra:totalItems' => 5,
            'hydra:view' => [
                '@id' => '/blog_posts?pagination=false&properties%5B%5D=slug&properties%5Bcategory%5D%5B%5D=slug',
                '@type' => 'hydra:PartialCollectionView',
            ],
            'hydra:search' => [
                '@type' => 'hydra:IriTemplate',
                'hydra:template' => '/blog_posts{?category.slug,category.slug[],tags.slug,tags.slug[],order[publishedAt],q,properties[]}',
                'hydra:variableRepresentation' => 'BasicRepresentation',
                'hydra:mapping' => [
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'category.slug',
                        'property' => 'category.slug',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'category.slug[]',
                        'property' => 'category.slug',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'tags.slug',
                        'property' => 'tags.slug',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'tags.slug[]',
                        'property' => 'tags.slug',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'order[publishedAt]',
                        'property' => 'publishedAt',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'q',
                        'property' => 'title, content, tags.name',
                        'required' => false,
                    ],
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'properties[]',
                        'property' => null,
                        'required' => false,
                    ],
                ],
            ],
        ]);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testOnEmptyPage(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_posts?page=999999');

        self::assertResponseStatusCodeSame(404);
        self::assertJsonContains([
            'hydra:description' => 'Page have no results',
        ]);
    }

    public function testOnFirstPageWithoutResults(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_posts?page=1&category.slug[]=zxcv');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@id' => '/blog_posts',
            '@type' => 'hydra:Collection',
            'hydra:member' => [],
            'hydra:totalItems' => 0,
        ]);
    }
}
