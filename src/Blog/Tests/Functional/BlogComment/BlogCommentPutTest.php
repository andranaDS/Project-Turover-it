<?php

namespace App\Blog\Tests\Functional\BlogComment;

use App\Tests\Functional\ApiTestCase;

class BlogCommentPutTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('PUT', '/blog_comments/1', [
            'json' => [
                'content' => 'Blog - Post 1 - Comment 1 - Updated',
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsNotOwner(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('vincent.van-gogh@free-work.fr');
        $client->request('PUT', '/blog_comments/1', [
            'json' => [
                'content' => 'Blog - Post 1 - Comment 1 - Updated',
            ],
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsOwner(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('PUT', '/blog_comments/1', [
            'json' => [
                'content' => 'Blog - Post 1 - Comment 1 - Updated',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('PUT', '/blog_comments/1', [
            'json' => [
                'content' => 'Blog - Post 1 - Comment 1 - Updated',
            ],
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    public function testWithValidData(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $response = $client->request('GET', '/blog_comments/1');
        $oldUpdatedAt = $response->toArray()['updatedAt'] ?? null;

        $response = $client->request('PUT', '/blog_comments/1', [
            'json' => [
                'content' => 'Blog - Post 1 - Comment 1 - Updated',
            ],
        ]);
        $newUpdatedAt = $response->toArray()['updatedAt'] ?? null;

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            'content' => 'Blog - Post 1 - Comment 1 - Updated',
        ]);
        self::assertNotSame($oldUpdatedAt, $newUpdatedAt);
    }

    public function testWithMissingData(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('PUT', '/blog_comments/1', [
            'json' => [
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

    public function testWithSensitiveContent(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('PUT', '/blog_comments/1', [
            'json' => [
                'content' => 'Blog - Post 1 - Comment Sensitive content 1 and sensitive content 2',
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
        self::assertEmailTextBodyContains($email, 'Blog - Post 1 - Comment **Sensitive content 1** and **sensitive content 2**');
    }

    public function testWithForbiddenContent(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('PUT', '/blog_comments/1', [
            'json' => [
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
