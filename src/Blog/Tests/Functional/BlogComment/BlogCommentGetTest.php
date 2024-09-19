<?php

namespace App\Blog\Tests\Functional\BlogComment;

use App\Tests\Functional\ApiTestCase;

class BlogCommentGetTest extends ApiTestCase
{
    public function testWithData(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_comments/1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/BlogComment',
            '@type' => 'BlogComment',
            'content' => 'Blog - Post 1 - Comment 1',
            'author' => [
                '@type' => 'User',
                'avatar' => [
                    'xSmall' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_x_small/monet-avatar.jpg',
                    'small' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_small/monet-avatar.jpg',
                    'medium' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_medium/monet-avatar.jpg',
                ],
                'displayAvatar' => true,
                'nickname' => 'Claude-Monet',
                'nicknameSlug' => 'claude-monet',
                'deleted' => false,
            ],
        ]);
    }

    public function testWithAuthorDeleted(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();

        $client->request('GET', '/blog_comments/9');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/BlogComment',
            '@type' => 'BlogComment',
            'content' => 'Blog - Post 4 - Comment 2',
            'author' => null,
        ]);
    }

    public function testWithInvalidPostId(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();

        $client->request('GET', '/blog_comments/1337');
        self::assertResponseStatusCodeSame(404);
    }
}
