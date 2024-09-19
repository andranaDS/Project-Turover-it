<?php

namespace App\Core\Tests\Functional\HomeStatistics;

use App\Tests\Functional\ApiTestCase;

class HomeStatisticsGetTodayTest extends ApiTestCase
{
    public function testCases(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/home/statistics');

        self::assertJsonContains([
            'visibleResumeCount' => 2,
            'jobPostingFreeCount' => 2,
            'jobPostingWorkCount' => 50,
            'turnoverItRecruitersCount' => 1000,
            'forumTopicsCount' => 9,
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');
    }
}
