<?php

namespace App\Blog\Tests\Functional\BlogComment;

use App\Tests\Functional\ApiTestCase;

class BlogCommentDeleteTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('DELETE', '/blog_comments/1');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsOwner(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('DELETE', '/blog_comments/1');

        self::assertResponseIsSuccessful();
    }

    public function testLoggedAsNotOwner(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('vincent.van-gogh@free-work.fr');
        $client->request('DELETE', '/blog_comments/1');

        self::assertResponseStatusCodeSame(403);
    }

    public function testNotDeleted(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/blog_comments/1');

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/contexts/BlogComment',
            '@type' => 'BlogComment',
            'content' => 'Blog - Post 1 - Comment 1',
        ]);

        $client->request('DELETE', '/blog_comments/1');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/blog_comments/1');
        self::assertResponseStatusCodeSame(404);
    }
}
