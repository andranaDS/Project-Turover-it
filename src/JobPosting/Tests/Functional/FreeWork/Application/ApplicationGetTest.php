<?php

namespace App\JobPosting\Tests\Functional\FreeWork\Application;

use App\JobPosting\Enum\ApplicationStep;
use App\JobPosting\Enum\RemoteMode;
use App\Tests\Functional\ApiTestCase;

class ApplicationGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/applications/1');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/applications/1');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('GET', '/applications/1');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsUserOnNonExistantApplication(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/applications/non-existant-application');

        self::assertResponseStatusCodeSame(404);
    }

    public function testLoggedAsUserOnItsOwnEntity(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/applications/1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Application',
            '@id' => '/applications/1',
            '@type' => 'Application',
            'content' => 'Job 1 - Application 1',
            'step' => ApplicationStep::RESUME,
            'state' => [
                'value' => 'in_progress',
                'label' => 'Candidature en cours',
            ],
            'createdAt' => '2021-01-01T10:00:00+01:00',
            'updatedAt' => '2021-01-02T10:00:00+01:00',
            'favoriteAt' => '2021-03-01T23:30:00+01:00',
            'jobPosting' => [
                'title' => 'Responsable applicatifs Finance (H/F) (CDI)',
                'minAnnualSalary' => 40000,
                'maxAnnualSalary' => 40000,
                'minDailySalary' => null,
                'maxDailySalary' => null,
                'currency' => 'GBP',
                'contracts' => ['permanent'],
                'duration' => 24,
                'renewable' => false,
                'remoteMode' => RemoteMode::NONE,
                'job' => [
                    'id' => 150,
                    'name' => 'Responsable d\'Applications Techniques',
                    'slug' => 'responsable-dapplications-techniques',
                ],
                'location' => [
                    '@type' => 'Location',
                    'street' => null,
                    'locality' => 'Paris',
                    'postalCode' => null,
                    'adminLevel1' => 'Île-de-France',
                    'adminLevel2' => null,
                    'country' => 'France',
                    'countryCode' => 'FR',
                    'latitude' => '48.8588897',
                    'longitude' => '2.3200410',
                    'key' => 'fr~ile-de-france~~paris',
                    'label' => 'Paris, Île-de-France',
                    'shortLabel' => 'Paris',
                ],
                'startsAt' => null,
                'company' => [
                    '@id' => '/companies/company-1',
                    '@type' => 'Company',
                    'id' => 1,
                    'name' => 'Company 1',
                    'slug' => 'company-1',
                    'logo' => [
                        'small' => 'https://api.freework.localhost/media/cache/resolve/company_logo_small/company-1-logo.jpg',
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/company_logo_medium/company-1-logo.jpg',
                    ],
                ],
                'skills' => [
                    [
                        '@id' => '/skills/1',
                        '@type' => 'Skill',
                        'id' => 1,
                        'name' => 'php',
                        'slug' => 'php',
                    ],
                    [
                        '@id' => '/skills/2',
                        '@type' => 'Skill',
                        'id' => 2,
                        'name' => 'java',
                        'slug' => 'java',
                    ],
                    [
                        '@id' => '/skills/3',
                        '@type' => 'Skill',
                        'id' => 3,
                        'name' => 'javascript',
                        'slug' => 'javascript',
                    ],
                ],
                'annualSalary' => "40k\u{a0}£GB",
                'dailySalary' => null,
                'applicationsCount' => 2,
            ],
            'company' => null,
            'documents' => [],
        ]);
    }

    public function testLoggedAsUserOnItsOwnEntityWithFilter(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/applications/1?properties[]=createdAt&&properties[]=updatedAt');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Application',
            '@id' => '/applications/1',
            '@type' => 'Application',
            'createdAt' => '2021-01-01T10:00:00+01:00',
            'updatedAt' => '2021-01-02T10:00:00+01:00',
        ]);
    }
}
