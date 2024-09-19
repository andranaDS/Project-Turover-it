<?php

namespace App\FeedRss\Tests\Functional;

use App\Tests\Functional\ApiTestCase;

class FeedRssGetTest extends ApiTestCase
{
    public function testFeedRssGet(): void
    {
        $client = static::createFreeWorkAuthenticatedClient();

        $client->request('GET', '/flux-rss/free-work-rss-neuvoo-contractor.xml');

        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('content-type', 'text/xml; charset=UTF-8');
    }
}
