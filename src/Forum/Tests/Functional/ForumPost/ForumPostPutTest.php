<?php

namespace App\Forum\Tests\Functional\ForumPost;

use App\Tests\Functional\ApiTestCase;

class ForumPostPutTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('PUT', '/forum_posts/1', [
            'json' => [
                'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 1 - Updated</p>',
                'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 1 - Post 1 - Updated"}]}]}',
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsNotOwner(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('vincent.van-gogh@free-work.fr');
        $client->request('PUT', '/forum_posts/1', [
            'json' => [
                'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 1 - Updated</p>',
                'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 1 - Post 1 - Updated"}]}]}',
            ],
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsOwner(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('PUT', '/forum_posts/1', [
            'json' => [
                'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 1 - Updated</p>',
                'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 1 - Post 1 - Updated"}]}]}',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('PUT', '/forum_posts/1', [
            'json' => [
                'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 1 - Updated</p>',
                'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 1 - Post 1 - Updated"}]}]}',
            ],
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    public function testWithValidData(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $response = $client->request('GET', '/forum_posts/1');
        $oldUpdatedAt = $response->toArray()['updatedAt'] ?? null;

        $response = $client->request('PUT', '/forum_posts/1', [
            'json' => [
                'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 1 - Updated</p>',
                'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 1 - Post 1 - Updated"}]}]}',
            ],
        ]);
        $newUpdatedAt = $response->toArray()['updatedAt'] ?? null;

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 1 - Updated</p>',
        ]);
        self::assertNotSame($oldUpdatedAt, $newUpdatedAt);
    }

    public function testWithMissingData(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('PUT', '/forum_posts/1', [
            'json' => [
                'contentHtml' => '',
                'contentJson' => '',
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
                    'propertyPath' => 'contentHtml',
                    'message' => 'Cette valeur ne doit pas être vide.',
                ],
                [
                    'propertyPath' => 'contentJson',
                    'message' => 'Cette valeur ne doit pas être vide.',
                ],
            ],
        ]);
    }

    public function testWithInvalidData(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('PUT', '/forum_posts/1', [
            'json' => [
                'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 1 - Updated</p>',
                'contentJson' => 'Not valid json',
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
                    'propertyPath' => 'contentJson',
                    'message' => 'Cette valeur doit être un JSON valide.',
                ],
            ],
        ]);
    }

    public function testWithSensitiveContent(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('PUT', '/forum_posts/1', [
            'json' => [
                'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 1 sensitive content 1, sensitive content 2 and sensitive content 3</p>',
                'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 1 - Post 1 sensitive content 1, sensitive content 2 and sensitive content 3"}]}]}',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $email = self::getMailerMessage();
        self::assertNotNull($email);
        self::assertEmailHeaderSame($email, 'from', 'Free-Work <contact@free-work.com>');
        self::assertEmailHeaderSame($email, 'to', 'marketing@free-work.com');
        self::assertEmailHeaderSame($email, 'subject', 'TEST: Contenu sensible détecté');
        self::assertEmailTextBodyContains($email, 'ForumPost');
        self::assertEmailTextBodyContains($email, 'Category 1.1 - Topic 1 - Post 1 **sensitive content 1**, **sensitive content 2** and **sensitive content 3**');
    }

    public function testWithForbiddenContent(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('PUT', '/forum_posts/1', [
            'json' => [
                'topic' => '/forum_topics/category-1-1-topic-1',
                'parent' => null,
                'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 1 forbidden content 1, forbidden content 2 and forbidden content 3</p>',
                'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 1 - Post 1 forbidden content 1, forbidden content 2 and forbidden content 3"}]}]}',
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
                    'propertyPath' => 'contentHtml',
                    'message' => 'La valeur est constitué d\'élement(s) interdit: "forbidden content 1", "forbidden content 2", "forbidden content 3".',
                ],
            ],
        ]);
    }

    public function testWithContentSanitizer(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('PUT', '/forum_posts/1', [
            'json' => [
                'topic' => '/forum_topics/category-1-1-topic-1',
                'parent' => null,
                'contentHtml' => '<iframe></iframe><p>Category 1.1 - Topic 1<script>alert("alert")</script></p><script>alert("alert")</script>',
                'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 1 - Post 3"}]}]}',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumPost',
            '@type' => 'ForumPost',
            'contentHtml' => '<p>Category 1.1 - Topic 1</p>',
        ]);
    }
}
