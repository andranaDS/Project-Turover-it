<?php

namespace App\Blog\Tests\Functional\BlogPostData;

use App\Tests\Functional\ApiTestCase;

class BlogPostDataGetTest extends ApiTestCase
{
    public function testData(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/blog_post_datas/1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/BlogPostData',
            '@id' => '/blog_post_datas/1',
            '@type' => 'BlogPostData',
            'id' => 1,
            'upvotesCount' => 3,
            'viewsCount' => 3,
            'recentViewsCount' => 0,
        ]);
    }

    public function testNotExists(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_post_datas/9999');

        self::assertResponseStatusCodeSame(404);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}
