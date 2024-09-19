<?php

namespace App\Core\Tests\Functional\Location;

use App\Tests\Functional\ApiTestCase;

class LocationKeyLabelsGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/location_key_labels');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/location_key_labels');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('GET', '/location_key_labels');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testData(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/location_key_labels');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/LocationKeyLabel',
            '@id' => '/location_key_labels',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/location_key_labels/fr~auvergne-rhone-alpes~seyssinet-pariset',
                    '@type' => 'LocationKeyLabel',
                    'key' => 'fr~auvergne-rhone-alpes~seyssinet-pariset',
                    'label' => 'Seyssinet-Pariset, Auvergne-Rhône-Alpes',
                ],
                [
                    '@id' => '/location_key_labels/fr~hauts-de-france~~le-touquet-paris-plage',
                    '@type' => 'LocationKeyLabel',
                    'key' => 'fr~hauts-de-france~~le-touquet-paris-plage',
                    'label' => 'Le Touquet-Paris-Plage, Hauts-de-France',
                ],
            ],
            'hydra:totalItems' => 5,
        ]);
    }

    public function testWithExistingKeyFilter(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/location_key_labels?key=fr~nouvelle-aquitaine~~bordeaux,fr~ile-de-france~~paris');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/LocationKeyLabel',
            '@id' => '/location_key_labels',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/location_key_labels/fr~ile-de-france~~paris',
                    '@type' => 'LocationKeyLabel',
                    'key' => 'fr~ile-de-france~~paris',
                    'label' => 'Paris, Île-de-France',
                ],
                [
                    '@id' => '/location_key_labels/fr~nouvelle-aquitaine~~bordeaux',
                    '@type' => 'LocationKeyLabel',
                    'key' => 'fr~nouvelle-aquitaine~~bordeaux',
                    'label' => 'Bordeaux, Nouvelle-Aquitaine',
                ],
            ],
            'hydra:totalItems' => 2,
        ]);
    }

    public function testWithNonExistingKeyFilter(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/location_key_labels?key=non~existant');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains([
            '@context' => '/contexts/LocationKeyLabel',
            '@id' => '/location_key_labels',
            '@type' => 'hydra:Collection',
            'hydra:member' => [],
            'hydra:totalItems' => 0,
        ]);
    }
}
