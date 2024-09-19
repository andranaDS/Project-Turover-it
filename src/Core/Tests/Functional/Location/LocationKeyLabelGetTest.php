<?php

namespace App\Core\Tests\Functional\Location;

use App\Tests\Functional\ApiTestCase;

class LocationKeyLabelGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/location_key_labels/fr~nouvelle-aquitaine~~bordeaux');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/location_key_labels/fr~nouvelle-aquitaine~~bordeaux');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('GET', '/location_key_labels/fr~nouvelle-aquitaine~~bordeaux');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testNotExists(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/location_key_labels/not~exists');

        self::assertResponseStatusCodeSame(404);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testExists(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/location_key_labels/fr~nouvelle-aquitaine~~bordeaux');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/LocationKeyLabel',
            '@id' => '/location_key_labels/fr~nouvelle-aquitaine~~bordeaux',
            '@type' => 'LocationKeyLabel',
            'key' => 'fr~nouvelle-aquitaine~~bordeaux',
            'label' => 'Bordeaux, Nouvelle-Aquitaine',
        ]);
    }
}
