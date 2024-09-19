<?php

namespace App\Blog\Tests\Functional\BlogComment;

use App\Tests\Functional\ApiTestCase;

class BlogCommentPostTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('POST', '/blog_comments', [
            'json' => [
                'post' => '/blog_posts/blog-category-1-post-1',
                'content' => 'Blog - Post 1 - Comment 4',
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('POST', '/blog_comments', [
            'json' => [
                'post' => '/blog_posts/blog-category-1-post-1',
                'content' => 'Blog - Post 1 - Comment 4',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('POST', '/blog_comments', [
            'json' => [
                'post' => '/blog_posts/blog-category-1-post-1',
                'content' => 'Blog - Post 1 - Comment 4',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testWithValidData(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('POST', '/blog_comments', [
            'json' => [
                'post' => '/blog_posts/blog-category-1-post-1',
                'content' => 'Blog - Post 1 - Comment 4',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/BlogComment',
            '@type' => 'BlogComment',
            'content' => 'Blog - Post 1 - Comment 4',
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

    public function testWithInvalidData(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('POST', '/blog_comments', [
            'json' => [
                'post' => '/blog_posts/blog-category-1-post-1',
                'content' => '',
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'violations' => [
                [
                    'propertyPath' => 'content',
                    'message' => 'Cette valeur ne doit pas être vide.',
                ],
            ],
        ]);
    }

    public function testWithMissingData(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('POST', '/blog_comments', [
            'json' => [
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'violations' => [
                [
                    'propertyPath' => 'content',
                    'message' => 'Cette valeur ne doit pas être vide.',
                ],
            ],
        ]);
    }

    public function testWithSensitiveContent(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('POST', '/blog_comments', [
            'json' => [
                'post' => '/blog_posts/blog-category-1-post-1',
                'content' => 'Blog - Post 1 - Comment Sensitive content 1 and Sensitive content 2',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $email = self::getMailerMessage();
        self::assertNotNull($email);

        self::assertEmailHeaderSame($email, 'from', 'Free-Work <contact@free-work.com>');
        self::assertEmailHeaderSame($email, 'to', 'marketing@free-work.com');
        self::assertEmailHeaderSame($email, 'subject', 'TEST: Contenu sensible détecté');
        self::assertEmailTextBodyContains($email, 'BlogComment');
        self::assertEmailTextBodyContains($email, 'Blog - Post 1 - Comment **Sensitive content 1** and **Sensitive content 2**');
    }

    public function testWithForbiddenContent(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('POST', '/blog_comments', [
            'json' => [
                'post' => '/blog_posts/blog-category-1-post-1',
                'content' => 'Blog - Post 1 - Comment forbidden content 1 and forbidden content 2',
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'violations' => [
                [
                    'propertyPath' => 'content',
                    'message' => 'La valeur est constitué d\'élement(s) interdit: "forbidden content 1", "forbidden content 2".',
                ],
            ],
        ]);
    }
}
