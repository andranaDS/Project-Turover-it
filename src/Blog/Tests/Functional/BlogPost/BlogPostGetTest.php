<?php

namespace App\Blog\Tests\Functional\BlogPost;

use App\Core\Enum\Locale;
use App\Tests\Functional\ApiTestCase;

class BlogPostGetTest extends ApiTestCase
{
    public function testPublished(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_posts/blog-category-1-post-2');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/BlogPost',
            '@id' => '/blog_posts/blog-category-1-post-2',
            '@type' => 'BlogPost',
            'id' => 2,
            'title' => 'Blog - Category 1 - Post 2',
            'slug' => 'blog-category-1-post-2',
            'excerpt' => 'Blog - Category 1 - Post 2 // Excerpt',
            'contentHtml' => '<p>Blog - Category 1 - Post 2 // Content</p>',
            'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Blog - Category 1 - Post 2 // Content"}]}]}',
            'metaTitle' => 'Blog - Category 1 - Post 2 // Meta title',
            'metaDescription' => 'Blog - Category 1 - Post 2 // Meta description',
            'category' => [
                '@type' => 'BlogCategory',
                'id' => 1,
                'name' => 'Blog - Category 1',
                'slug' => 'blog-category-1',
            ],
            'tags' => [
                [
                    '@type' => 'BlogTag',
                    'id' => 1,
                    'name' => 'Blog - Tag 1',
                    'slug' => 'blog-tag-1',
                ],
                [
                    '@type' => 'BlogTag',
                    'id' => 2,
                    'name' => 'Blog - Tag 2',
                    'slug' => 'blog-tag-2',
                ],
            ],
            'readingTimeMinutes' => 1,
            'image' => [
                'medium' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_medium/cat1art2-image.jpg',
                'large' => 'https://api.freework.localhost/media/cache/resolve/blog_post_image_large/cat1art2-image.jpg',
            ],
            'imageAlt' => 'Blog - Category 1 - Post 2 // Image alt',
            'publishedAt' => '2021-01-01T11:00:00+01:00',
            'updatedAt' => '2021-01-01T11:00:00+01:00',
            'modified' => true,
            'locales' => [
                Locale::fr_FR,
                Locale::fr_BE,
                Locale::fr_CH,
                Locale::fr_LU,
            ],
        ]);
    }

    public function testNotPublished(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_posts/blog-category-3-post-2-not-published');

        self::assertResponseStatusCodeSame(404);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testPublishedInThePast(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_posts/blog-category-1-post-1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/BlogPost',
            '@id' => '/blog_posts/blog-category-1-post-1',
            '@type' => 'BlogPost',
            'id' => 1,
            'title' => 'Blog - Category 1 - Post 1',
            'slug' => 'blog-category-1-post-1',
            'excerpt' => 'Blog - Category 1 - Post 1 // Excerpt',
            'contentHtml' => '<p>Blog - Category 1 - Post 1 // Content</p>',
            'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Blog - Category 1 - Post 1 // Content"}]}]}',
            'metaTitle' => 'Blog - Category 1 - Post 1 // Meta title',
            'metaDescription' => 'Blog - Category 1 - Post 1 // Meta description',
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
            'locales' => [
                Locale::fr_FR,
                Locale::fr_BE,
                Locale::fr_CH,
                Locale::fr_LU,
            ],
        ]);
    }

    public function testPublishedInTheFuture(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_posts/blog-category-3-post-3-published-in-the-future');

        self::assertResponseStatusCodeSame(404);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testNotExists(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_posts/not-exists');

        self::assertResponseStatusCodeSame(404);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testWithGoodLocale(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_posts/blog-category-1-post-1', [
            'headers' => [
                'accept-language' => 'fr-lu',
            ],
        ]);

        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testWithWrongLocale(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_posts/blog-category-1-post-1', [
            'headers' => [
                'accept-language' => 'en-gb',
            ],
        ]);

        self::assertResponseStatusCodeSame(404);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}
