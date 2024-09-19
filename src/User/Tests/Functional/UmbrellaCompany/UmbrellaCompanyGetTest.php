<?php

namespace App\User\Tests\Functional\UmbrellaCompany;

use App\Tests\Functional\ApiTestCase;

class UmbrellaCompanyGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/umbrella_companies/1');

        self::assertResponseStatusCodeSame(200);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/umbrella_companies/1');

        self::assertResponseStatusCodeSame(200);
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('GET', '/umbrella_companies/1');

        self::assertResponseStatusCodeSame(200);
    }

    public function testOnNonExistantUmbrellaCompany(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/umbrella_companies/non-existant-umbrella-company');

        self::assertResponseStatusCodeSame(404);
    }

    public function testData(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/umbrella_companies/1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/UmbrellaCompany',
            '@id' => '/umbrella_companies/1',
            '@type' => 'UmbrellaCompany',
            'id' => 1,
            'name' => 'Umbrella Company 1',
            'slug' => 'umbrella-company-1',
        ]);
    }
}
