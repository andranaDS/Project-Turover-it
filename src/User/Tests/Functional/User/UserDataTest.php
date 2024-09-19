<?php

namespace App\User\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;

class UserDataTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();

        $client->request('GET', '/users/1/data');
        self::assertResponseStatusCodeSame(401);

        $client->request('GET', '/users/2/data');
        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();

        // own id
        $client->request('GET', '/users/1/data');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        // other id
        $client->request('GET', '/users/2/data');
        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();

        // own id
        $client->request('GET', '/users/2/data');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        // other id
        $client->request('GET', '/users/1/data');
        self::assertResponseStatusCodeSame(403);
    }

    public function testWithoutScopes(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $response = $client->request('GET', '/users/6/data');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');
        self::assertJsonContains([
            'blog_post_upvotes' => [2, 6],
            'forum_topic_traces' => [],
            'forum_topic_favorites' => [1, 7],
            'forum_topic_participations' => [1, 2, 4, 5, 6, 7, 8],
            'forum_post_upvotes' => [2, 17],
            'company_favorites' => [1, 3, 5],
            'company_blacklists' => [],
            'job_posting_favorites' => [1, 2],
            'job_posting_application_in_progress' => [1, 2, 3],
            'job_posting_application_ko' => [],
            'job_posting_traces' => [1],
        ]);

        $content = json_decode($response->getContent(), true);

        self::assertArrayHasKey(1, $content['forum_topic_traces']);
        self::assertArrayHasKey(2, $content['forum_topic_traces']);
        self::assertArrayHasKey(7, $content['forum_topic_traces']);
        self::assertArrayHasKey(8, $content['forum_topic_traces']);

        $client = static::createFreeWorkAuthenticatedClient('pablo.picasso@free-work.fr');
        $client->request('GET', '/users/10/data');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');
        self::assertJsonContains([
            'blog_post_upvotes' => [1, 5],
            'forum_topic_traces' => [
                4 => '2021-01-10T23:00:00+01:00',
            ],
            'forum_topic_favorites' => [],
            'forum_topic_participations' => [],
            'forum_post_upvotes' => [5],
            'company_favorites' => [],
            'company_blacklists' => [2],
            'job_posting_favorites' => [],
            'job_posting_application_in_progress' => [],
            'job_posting_application_ko' => [],
            'job_posting_traces' => [],
        ]);
    }

    public function testWithScope(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/users/6/data?scopes=blog_post_upvotes');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');
        self::assertJsonContains([
            'blog_post_upvotes' => [2, 6],
        ]);
    }

    public function testWithScopes(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/users/6/data?scopes=forum_topic_favorites');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');
        self::assertJsonContains([
            'forum_topic_favorites' => [1, 7],
        ]);

        $client = static::createFreeWorkAuthenticatedClient('pablo.picasso@free-work.fr');
        $client->request('GET', '/users/10/data?scopes=forum_topic_traces');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');
        self::assertJsonContains([
            'forum_topic_traces' => [
                4 => '2021-01-10T23:00:00+01:00',
            ],
        ]);
    }
}
