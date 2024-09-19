<?php

namespace App\Forum\Tests\Functional\ForumPostReport;

use App\Tests\Functional\ApiTestCase;

class ForumPostReportGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/forum_post_reports/1');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('GET', '/forum_post_reports/1');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsOwner(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/forum_post_reports/1');

        self::assertResponseIsSuccessful();
    }

    public function testLoggedAsNotOwner(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('vincent.van-gogh@free-work.fr');
        $client->request('GET', '/forum_post_reports/1');

        self::assertResponseStatusCodeSame(403);
    }
}
