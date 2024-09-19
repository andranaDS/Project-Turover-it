<?php

namespace App\Forum\Tests\Functional\ForumTopicData;

use App\Tests\Functional\ApiTestCase;

class ForumTopicDataGetByTopicsGetTest extends ApiTestCase
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

        $client->request('GET', '/forum_topic_datas');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testData(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/forum_topic_datas?ids=1,2,3');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');
        self::assertJsonContains([
            '1' => [
                'repliesCount' => 1,
                'viewsCount' => 4,
            ],
            '2' => [
                'repliesCount' => 6,
                'viewsCount' => 1,
            ],
            '3' => [
                'repliesCount' => 0,
                'viewsCount' => 0,
            ],
        ]);
    }

    public function testEmpty(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/forum_topic_datas');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');
        self::assertJsonEquals([]);
    }
}
