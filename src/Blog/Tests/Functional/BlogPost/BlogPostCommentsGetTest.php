<?php

namespace App\Blog\Tests\Functional\BlogPost;

use App\Tests\Functional\ApiTestCase;

class BlogPostCommentsGetTest extends ApiTestCase
{
    public function testWithExistingPost(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_posts/blog-category-1-post-1/comments');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains([
            '@context' => '/contexts/BlogComment',
            '@id' => '/blog_posts/blog-category-1-post-1/comments',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@type' => 'BlogComment',
                    'content' => 'Blog - Post 1 - Comment 3',
                    'createdAt' => '2021-01-01T12:00:00+01:00',
                    'author' => [
                        '@type' => 'User',
                        'avatar' => null,
                        'displayAvatar' => false,
                        'nickname' => 'Vincent-van-Gogh',
                        'nicknameSlug' => 'vincent-van-gogh',
                        'deleted' => false,
                    ],
                ],
                [
                    '@type' => 'BlogComment',
                    'content' => 'Blog - Post 1 - Comment 2',
                    'createdAt' => '2021-01-01T11:00:00+01:00',
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
                ],
            ],
            'hydra:totalItems' => 3,
        ]);
    }

    public function testWithNonExistentPost(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_posts/blog-category-non-existent/comments');

        self::assertResponseStatusCodeSame(404);
    }

    public function testWithAuthorDeleted(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_posts/blog-category-1-post-4/comments');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains([
            '@context' => '/contexts/BlogComment',
            '@id' => '/blog_posts/blog-category-1-post-4/comments',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@type' => 'BlogComment',
                    'content' => 'Blog - Post 4 - Comment 2',
                    'author' => null,
                ],
            ],
            'hydra:totalItems' => 1,
        ]);
    }
}
