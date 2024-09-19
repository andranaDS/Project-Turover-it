<?php

namespace App\Blog\Tests\Functional\BlogTag;

use App\Tests\Functional\ApiTestCase;

class BlogTagPostsGetTest extends ApiTestCase
{
    public function testWithExistingTag(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_tags/blog-tag-1/posts');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/BlogPost',
            '@id' => '/blog_tags/blog-tag-1/posts',
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
                '@id' => '/blog_tags/blog-tag-1/posts?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/blog_tags/blog-tag-1/posts?page=1',
                'hydra:last' => '/blog_tags/blog-tag-1/posts?page=3',
                'hydra:next' => '/blog_tags/blog-tag-1/posts?page=2',
            ],
        ]);
    }

    public function testWithNonExistentTag(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_tags/blog-tag-non-existent/posts');

        self::assertResponseStatusCodeSame(404);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}
