<?php

namespace App\Forum\Tests\Functional\ForumPost;

use App\Tests\Functional\ApiTestCase;

class ForumPostModerateTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('PATCH', '/forum_posts/1/moderate');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('PATCH', '/forum_posts/1/moderate');

        self::assertResponseIsSuccessful();
    }

    public function testLoggedAsOwner(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('PATCH', '/forum_posts/1/moderate');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsNotOwner(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('vincent.van-gogh@free-work.fr');
        $client->request('PATCH', '/forum_posts/1/moderate');

        self::assertResponseStatusCodeSame(403);
    }

    public function testNotModerated(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();

        $client->request('GET', '/forum_posts/1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumPost',
            '@type' => 'ForumPost',
            'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 1 - Lorem</p>',
            'moderated' => false,
        ]);

        $client->request('PATCH', '/forum_posts/1/moderate');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumPost',
            '@type' => 'ForumPost',
            'contentHtml' => null,
            'moderated' => true,
        ]);
    }

    public function testAlreadyModerated(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();

        $client->request('GET', '/forum_posts/14');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumPost',
            '@type' => 'ForumPost',
            'contentHtml' => null,
            'moderated' => true,
        ]);

        $client->request('PATCH', '/forum_posts/14/moderate');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumPost',
            '@type' => 'ForumPost',
            'contentHtml' => null,
            'moderated' => true,
        ]);
    }
}
