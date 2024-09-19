<?php

namespace App\User\Tests\Functional\UmbrellaCompany;

use App\Tests\Functional\ApiTestCase;

class UmbrellaCompaniesGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/umbrella_companies');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/umbrella_companies');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('GET', '/umbrella_companies');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testResult(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/umbrella_companies');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/UmbrellaCompany',
            '@id' => '/umbrella_companies',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    'name' => 'Umbrella Company 1',
                    'slug' => 'umbrella-company-1',
                ],
                [
                    'name' => 'Umbrella Company 2',
                    'slug' => 'umbrella-company-2',
                ],
            ],
            'hydra:totalItems' => 5,
            'hydra:view' => [
                '@id' => '/umbrella_companies?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/umbrella_companies?page=1',
                'hydra:last' => '/umbrella_companies?page=3',
                'hydra:next' => '/umbrella_companies?page=2',
            ],
        ]);
    }

    public function testSearchWithResult(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/umbrella_companies?name=sear');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/UmbrellaCompany',
            '@id' => '/umbrella_companies',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    'name' => 'Umbrella Company 5 search',
                    'slug' => 'umbrella-company-5-search',
                ],
            ],
            'hydra:totalItems' => 1,
        ]);
    }

    public function testSearchWithoutResult(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/umbrella_companies?name=wxc');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/UmbrellaCompany',
            '@id' => '/umbrella_companies',
            '@type' => 'hydra:Collection',
            'hydra:member' => [],
            'hydra:totalItems' => 0,
        ]);
    }
}
