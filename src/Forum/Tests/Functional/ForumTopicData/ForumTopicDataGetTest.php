<?php

namespace App\Forum\Tests\Functional\ForumTopicData;

use App\Tests\Functional\ApiTestCase;

class ForumTopicDataGetTest extends ApiTestCase
{
    public static function provideLoggedAndNotLoggedCases(): iterable
    {
        yield ['user@free-work.fr'];
        yield ['admin@free-work.fr'];
        yield [null];
    }

    /**
     * @dataProvider provideLoggedAndNotLoggedCases
     */
    public function testLoggedAndNotLogged(?string $email): void
    {
        if (null !== $email) {
            $client = self::createFreeWorkAuthenticatedClient($email);
        } else {
            $client = self::createFreeWorkClient();
        }

        $client->request('GET', '/forum_topic_datas/1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testData(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/forum_topic_datas/1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumTopicData',
            '@id' => '/forum_topic_datas/1',
            '@type' => 'ForumTopicData',
            'id' => 1,
            'upvotesCount' => 2,
            'viewsCount' => 4,
        ]);
    }

    public function testNotExists(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/forum_topic_datas/9999');

        self::assertResponseStatusCodeSame(404);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}
