<?php

namespace App\Forum\Tests\Functional\ForumTopic;

use App\Tests\Functional\ApiTestCase;

class ForumTopicsParticipationsTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/forum_topics/participations');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/forum_topics/participations');

        self::assertResponseIsSuccessful();
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('GET', '/forum_topics/participations');

        self::assertResponseIsSuccessful();
    }

    public function testWithoutSite(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/forum_topics/participations');

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/contexts/ForumTopic',
            '@id' => '/forum_topics',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@type' => 'ForumTopic',
                    'title' => 'Category 1.1 - Topic 2',
                ],
                [
                    '@type' => 'ForumTopic',
                    'title' => 'Category 4.1 - Topic 1',
                ],
            ],
            'hydra:totalItems' => 7,
            'hydra:view' => [
                '@id' => '/forum_topics/participations?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/forum_topics/participations?page=1',
                'hydra:last' => '/forum_topics/participations?page=4',
                'hydra:next' => '/forum_topics/participations?page=2',
            ],
        ]);
    }

    public function testMostRecent(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/forum_topics/participations?order[lastPost.createdAt]=desc');

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/contexts/ForumTopic',
            '@id' => '/forum_topics',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@type' => 'ForumTopic',
                    'title' => 'Category 1.1 - Topic 2',
                ],
                [
                    '@type' => 'ForumTopic',
                    'title' => 'Category 4.1 - Topic 1',
                ],
            ],
            'hydra:totalItems' => 7,
            'hydra:view' => [
                '@id' => '/forum_topics/participations?order%5BlastPost.createdAt%5D=desc&page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/forum_topics/participations?order%5BlastPost.createdAt%5D=desc&page=1',
                'hydra:last' => '/forum_topics/participations?order%5BlastPost.createdAt%5D=desc&page=4',
                'hydra:next' => '/forum_topics/participations?order%5BlastPost.createdAt%5D=desc&page=2',
            ],
        ]);
    }

    public function testOldest(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/forum_topics/participations?order[lastPost.createdAt]=asc');

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/contexts/ForumTopic',
            '@id' => '/forum_topics',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@type' => 'ForumTopic',
                    'id' => 1,
                    'title' => 'Category 1.1 - Topic 1',
                ],
                [
                    '@type' => 'ForumTopic',
                    'id' => 4,
                    'title' => 'Category 1.2 - Topic 1',
                ],
            ],
            'hydra:totalItems' => 7,
            'hydra:view' => [
                '@id' => '/forum_topics/participations?order%5BlastPost.createdAt%5D=asc&page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/forum_topics/participations?order%5BlastPost.createdAt%5D=asc&page=1',
                'hydra:last' => '/forum_topics/participations?order%5BlastPost.createdAt%5D=asc&page=4',
                'hydra:next' => '/forum_topics/participations?order%5BlastPost.createdAt%5D=asc&page=2',
            ],
        ]);
    }

    public function testWithoutParticipation(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('pablo.picasso@free-work.fr');
        $client->request('GET', '/forum_topics/participations');

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/contexts/ForumTopic',
            '@id' => '/forum_topics',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 0,
        ]);
    }
}
