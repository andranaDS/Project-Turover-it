<?php

namespace App\Forum\Tests\Functional\Forum;

use App\Tests\Functional\ApiTestCase;

class ForumStatisticsTest extends ApiTestCase
{
    public function test(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/forum/statistics');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');
        self::assertJsonEquals([
            'topicsCount' => 9,
            'postsCount' => 19,
            'recentPostsCount' => 4,
            'contributorsCount' => 6,
            'forumActiveUsersCount' => 2,
        ]);
    }
}
