<?php

namespace App\User\Tests\Functional\CompanyBusinessActivity;

use App\Tests\Functional\ApiTestCase;

class CompanyBusinessActivityGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $expected = [
            '@context' => '/contexts/CompanyBusinessActivity',
            '@id' => '/company_business_activities/1',
            '@type' => 'CompanyBusinessActivity',
            'id' => 1,
            'name' => 'Business activity 1',
            'slug' => 'business-activity-1',
        ];

        $client = static::createFreeWorkClient();
        $client->request('GET', '/company_business_activities/1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains($expected);

        $client = static::createTurnoverClient();
        $client->request('GET', '/company_business_activities/1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains($expected);
    }

    public function testNotExists(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/company_business_activities/not-exists');

        self::assertResponseStatusCodeSame(404);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $client = static::createTurnoverClient();
        $client->request('GET', '/company_business_activities/not-exists');

        self::assertResponseStatusCodeSame(404);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}
