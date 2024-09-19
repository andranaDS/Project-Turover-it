<?php

namespace App\User\Tests\Functional\InsuranceCompany;

use App\Tests\Functional\ApiTestCase;

class InsuranceCompanyGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/insurance_companies/1');

        self::assertResponseStatusCodeSame(200);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/insurance_companies/1');

        self::assertResponseStatusCodeSame(200);
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('GET', '/insurance_companies/1');

        self::assertResponseStatusCodeSame(200);
    }

    public function testOnNonExistantUmbrellaCompany(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/insurance_companies/non-existant-insurance-company');

        self::assertResponseStatusCodeSame(404);
    }

    public function testData(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/insurance_companies/1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/InsuranceCompany',
            '@id' => '/insurance_companies/1',
            '@type' => 'InsuranceCompany',
            'id' => 1,
            'name' => 'Insurance Company 1',
            'slug' => 'insurance-company-1',
        ]);
    }

    public function testWithWrongLocale(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/insurance_companies/1', [
            'headers' => [
                'accept-language' => 'en-gb',
            ],
        ]);

        self::assertResponseStatusCodeSame(404);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}
