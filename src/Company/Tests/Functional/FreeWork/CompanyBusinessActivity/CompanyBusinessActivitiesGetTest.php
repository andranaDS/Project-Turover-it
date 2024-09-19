<?php

namespace App\Company\Tests\Functional\FreeWork\CompanyBusinessActivity;

use App\Tests\Functional\ApiTestCase;

class CompanyBusinessActivitiesGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/company_business_activities');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/CompanyBusinessActivity',
            '@id' => '/company_business_activities',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/company_business_activities/1',
                    '@type' => 'CompanyBusinessActivity',
                    'id' => 1,
                    'name' => 'Business activity 1',
                    'slug' => 'business-activity-1',
                ],
                [
                    '@id' => '/company_business_activities/2',
                    '@type' => 'CompanyBusinessActivity',
                    'id' => 2,
                    'name' => 'Business activity 2',
                    'slug' => 'business-activity-2',
                ],
                [
                    '@id' => '/company_business_activities/3',
                    '@type' => 'CompanyBusinessActivity',
                    'id' => 3,
                    'name' => 'Business activity 3',
                    'slug' => 'business-activity-3',
                ],
            ],
            'hydra:totalItems' => 3,
        ]);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/company_business_activities');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('GET', '/company_business_activities');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}
