<?php

namespace App\Company\Tests\Functional\Turnover\Site;

use App\Tests\Functional\ApiTestCase;

class SiteGetTest extends ApiTestCase
{
    public function testByIdNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/sites/1');

        self::assertResponseStatusCodeSame(401);
    }

    public function testByIdLoggedNotOnMe(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/sites/3');

        self::assertResponseStatusCodeSame(403);
    }

    public function testByIdSlugLoggedNotOnMe(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/sites/-');

        self::assertResponseStatusCodeSame(404);
    }

    public function testByIdLoggedOnMe(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/sites/1');

        self::assertResponseStatusCodeSame(200);
        self::assertJsonContains([
            '@context' => '/contexts/Site',
            '@id' => '/sites/1',
            '@type' => 'Site',
            'id' => 1,
            'name' => 'Site 1 - Company 1',
            'slug' => 'site-1-company-1',
            'ip' => '1.1.1.1',
            'createdAt' => '2022-01-01T10:00:00+01:00',
            'updatedAt' => '2022-01-01T10:30:00+01:00',
        ]);
    }
}
