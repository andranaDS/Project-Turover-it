<?php

namespace App\User\Tests\Functional\InsuranceCompany;

use App\Tests\Functional\ApiTestCase;

class InsuranceCompaniesGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/insurance_companies');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/insurance_companies');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('GET', '/insurance_companies');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testResult(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/insurance_companies');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/InsuranceCompany',
            '@id' => '/insurance_companies',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    'name' => 'Insurance Company 1',
                    'slug' => 'insurance-company-1',
                ],
                [
                    'name' => 'Insurance Company 2',
                    'slug' => 'insurance-company-2',
                ],
            ],
            'hydra:totalItems' => 5,
            'hydra:view' => [
                '@id' => '/insurance_companies?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/insurance_companies?page=1',
                'hydra:last' => '/insurance_companies?page=3',
                'hydra:next' => '/insurance_companies?page=2',
            ],
        ]);
    }

    public function testSearchWithResult(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/insurance_companies?name=sear');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/InsuranceCompany',
            '@id' => '/insurance_companies',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    'name' => 'Insurance Company 5 search',
                    'slug' => 'insurance-company-5-search',
                ],
            ],
            'hydra:totalItems' => 1,
        ]);
    }

    public function testSearchWithoutResult(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/insurance_companies?name=wxc');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/InsuranceCompany',
            '@id' => '/insurance_companies',
            '@type' => 'hydra:Collection',
            'hydra:member' => [],
            'hydra:totalItems' => 0,
        ]);
    }
}
