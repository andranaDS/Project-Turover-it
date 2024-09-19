<?php

namespace App\Blog\Tests\Functional\BlogTag;

use App\Tests\Functional\ApiTestCase;

class BlogTagGetTest extends ApiTestCase
{
    public function testExists(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_tags/blog-tag-1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/BlogTag',
            '@id' => '/blog_tags/blog-tag-1',
            '@type' => 'BlogTag',
            'id' => 1,
            'name' => 'Blog - Tag 1',
            'slug' => 'blog-tag-1',
            'metaTitle' => 'Blog - Tag 1 // Meta title',
            'metaDescription' => 'Blog - Tag 1 // Meta description',
        ]);
    }

    public function testNotExists(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_tags/blog-tag-non-existent');

        self::assertResponseStatusCodeSame(404);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}
