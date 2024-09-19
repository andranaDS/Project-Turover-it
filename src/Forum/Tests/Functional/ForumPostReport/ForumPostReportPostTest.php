<?php

namespace App\Forum\Tests\Functional\ForumPostReport;

use App\Tests\Functional\ApiTestCase;

class ForumPostReportPostTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('POST', '/forum_post_reports', [
            'json' => [
                'post' => '/forum_posts/1',
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('POST', '/forum_post_reports', [
            'json' => [
                'post' => '/forum_posts/1',
            ],
        ]);

        self::assertResponseIsSuccessful();
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('POST', '/forum_post_reports', [
            'json' => [
                'post' => '/forum_posts/1',
            ],
        ]);

        self::assertResponseIsSuccessful();
    }

    public function testWithoutPost(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('POST', '/forum_post_reports', [
            'json' => [
                'post' => null,
            ],
        ]);

        self::assertResponseStatusCodeSame(400);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Error',
            '@type' => 'hydra:Error',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'Expected IRI or nested document for attribute "post", "NULL" given.',
        ]
        );
    }

    public function testWithValidData(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('POST', '/forum_post_reports', [
            'json' => [
                'post' => '/forum_posts/1',
                'content' => null,
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumPostReport',
            '@type' => 'ForumPostReport',
            'content' => null,
            'post' => '/forum_posts/1',
        ]
        );
    }

    public function testWithValidDataAndContent(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('POST', '/forum_post_reports', [
            'json' => [
                'post' => '/forum_posts/2',
                'content' => 'Report content',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumPostReport',
            '@type' => 'ForumPostReport',
            'content' => 'Report content',
            'post' => '/forum_posts/2',
        ]
        );
    }
}
