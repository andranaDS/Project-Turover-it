<?php

namespace App\JobPosting\Tests\Functional\FreeWork\Application;

use App\JobPosting\Enum\ApplicationStep;
use App\JobPosting\Enum\Contract;
use App\JobPosting\Enum\RemoteMode;
use App\Tests\Functional\ApiTestCase;
use Symfony\Component\String\ByteString;

class ApplicationPutTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('PUT', '/applications/1', [
            'json' => [
                'step' => ApplicationStep::CANCELLED,
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsNotOwner(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('vincent.van-gogh@free-work.fr');
        $client->request('PUT', '/applications/1', [
            'json' => [
                'step' => ApplicationStep::CANCELLED,
            ],
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsOwner(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('PUT', '/applications/1', [
            'json' => [
                'step' => ApplicationStep::CANCELLED,
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('PUT', '/applications/1', [
            'json' => [
                'step' => ApplicationStep::CANCELLED,
            ],
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    public function testWithValidData(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('PUT', '/applications/1', [
            'json' => [
                'step' => ApplicationStep::CANCELLED,
            ],
        ]);

        self::assertJsonContains([
            '@context' => '/contexts/Application',
            '@type' => 'Application',
            'step' => ApplicationStep::CANCELLED,
            'state' => [
                'value' => 'cancelled',
                'label' => 'Candidature abandonée',
            ],
            'content' => 'Job 1 - Application 1',
            'favoriteAt' => '2021-03-01T23:30:00+01:00',
            'seenAt' => null,
            'jobPosting' => [
                'title' => 'Responsable applicatifs Finance (H/F) (CDI)',
                'minAnnualSalary' => 40000,
                'maxAnnualSalary' => 40000,
                'minDailySalary' => null,
                'maxDailySalary' => null,
                'currency' => 'GBP',
                'contracts' => [Contract::PERMANENT],

                'duration' => 24,

                'renewable' => false,
                'remoteMode' => RemoteMode::NONE,
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
                'annualSalary' => "40k\u{a0}£GB",
                'dailySalary' => null,
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
                'applicationsCount' => 2,
            ],
            'company' => null,
            'documents' => [],
        ]);
    }

    public function testWithInvalidData(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('PUT', '/applications/1', [
            'json' => [
                'step' => ByteString::fromRandom(20),
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'violations' => [
                [
                    'propertyPath' => 'step',
                    'message' => 'Cette valeur doit être l\'un des choix proposés.',
                ],
            ],
        ]);
    }
}
