<?php

namespace App\Core\Tests\Functional\Healthz;

use App\Tests\Functional\ApiTestCase;

class HealthzTest extends ApiTestCase
{
    public function testTurnoverHealthz(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/healthz');

        self::assertResponseStatusCodeSame(200);
    }

    public function testFreeWorkHealthz(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/healthz');

        self::assertResponseStatusCodeSame(200);
    }
}
