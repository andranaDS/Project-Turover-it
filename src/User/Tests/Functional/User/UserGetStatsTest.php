<?php

namespace App\User\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;

class UserGetStatsTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();

        $client->request('GET', '/users/1/stats');
        self::assertResponseStatusCodeSame(401);

        $client->request('GET', '/users/2/stats');
        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();

        // own id
        $client->request('GET', '/users/1/stats');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        // other id
        $client->request('GET', '/users/2/stats');
        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();

        // own id
        $client->request('GET', '/users/2/stats');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        // other id
        $client->request('GET', '/users/1/stats');
        self::assertResponseStatusCodeSame(403);
    }

    public static function provideMandatoryDataCases(): iterable
    {
        return [
            [
                [
                    'userProfileViews' => 50,
                    'applicationsCount' => 3,
                    'jobPostingSuggestedCount' => 5,
                    'todayJobPostingFreeCount' => 0,
                    'todayJobPostingWorkCount' => 1,
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideMandatoryDataCases
     */
    public function testMandatoryData(array $expected): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/users/6/stats');

        self::assertJsonContains($expected);

        self::assertResponseIsSuccessful();
    }
}
