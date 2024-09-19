<?php

namespace App\Forum\Tests\Functional\ForumTopic;

use App\Tests\Functional\ApiTestCase;

class ForumTopicsFavoritesGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/forum_topics/favorites');

        self::assertResponseStatusCodeSame(401);
    }

    public function testWithFavorites(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/forum_topics/favorites');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumTopic',
            '@id' => '/forum_topics',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@type' => 'ForumTopic',
                    'title' => 'Category 4.1 - Topic 1',
                ],
                [
                    '@type' => 'ForumTopic',
                    'title' => 'Category 1.1 - Topic 1',
                ],
            ],
            'hydra:totalItems' => 2,
        ]);
    }

    public function testWithoutFavorite(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('vincent.van-gogh@free-work.fr');
        $client->request('GET', '/forum_topics/favorites');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumTopic',
            '@id' => '/forum_topics',
            '@type' => 'hydra:Collection',
            'hydra:member' => [],
            'hydra:totalItems' => 0,
        ]);
    }
}
