<?php

namespace App\Blog\Tests\Functional\BlogCategory;

use App\Core\Enum\Locale;
use App\Tests\Functional\ApiTestCase;

class BlogCategoryGetTest extends ApiTestCase
{
    public function testWithData(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_categories/blog-category-1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/BlogCategory',
            '@type' => 'BlogCategory',
            'name' => 'Blog - Category 1',
            'description' => 'Blog - Category 1 // Description',
            'metaTitle' => 'Blog - Category 1 // Meta title',
            'metaDescription' => 'Blog - Category 1 // Meta description',
            'postsCount' => 4,
            'locales' => [
                Locale::fr_FR,
                Locale::fr_BE,
                Locale::fr_CH,
                Locale::fr_LU,
            ],
        ]);
    }

    public function testWithInvalidPostId(): void
    {
        $client = static::createFreeWorkClient();

        $client->request('GET', '/blog_categories/blog-category-1337');
        self::assertResponseStatusCodeSame(404);
    }

    public function testWithGoodLocale(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_categories/blog-category-1', [
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
        $client->request('GET', '/blog_categories/blog-category-1', [
            'headers' => [
                'accept-language' => 'en-gb',
            ],
        ]);

        self::assertResponseStatusCodeSame(404);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}
