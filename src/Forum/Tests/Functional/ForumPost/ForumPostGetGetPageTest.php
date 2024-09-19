<?php

namespace App\Forum\Tests\Functional\ForumPost;

use App\Tests\Functional\ApiTestCase;

class ForumPostGetGetPageTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();

        $client->request('GET', '/forum_posts/1/page');
        self::assertResponseIsSuccessful();
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();

        $client->request('GET', '/forum_posts/1/page');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();

        $client->request('GET', '/forum_posts/1/page');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');
    }

    public static function provideDataCases(): iterable
    {
        return [
            [
                [
                    'page' => 2,
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideDataCases
     */
    public function testData(array $expected): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/forum_posts/9/page?itemsPerPage=1');

        self::assertJsonContains($expected);

        self::assertResponseIsSuccessful();
    }
}
