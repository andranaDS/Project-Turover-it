<?php

namespace App\User\Tests\Functional\User;

use App\JobPosting\Enum\ApplicationStep;
use App\JobPosting\Enum\RemoteMode;
use App\Tests\Functional\ApiTestCase;

class UserApplicationsGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/users/1/applications');

        self::assertResponseStatusCodeSame(401);
    }

    public function testWithNonExistentUser(): void
    {
        $client = static::createFreeWorkAuthenticatedClient();
        $client->request('GET', '/users/user-non-existent/applications');

        self::assertResponseStatusCodeSame(403);
    }

    public function testOnInOtherEntity(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/users/3/applications');

        self::assertResponseStatusCodeSame(403);
    }

    public function testWithoutApplications(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('elisabeth.vigee-le-brun@free-work.fr');
        $client->request('GET', '/users/11/applications');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Application',
            '@id' => '/users/11/applications',
            '@type' => 'hydra:Collection',
            'hydra:member' => [],
            'hydra:totalItems' => 0,
        ]);
    }

    public function testWithApplicationsOnJobPostings(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/users/6/applications');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Application',
            '@id' => '/users/6/applications',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@type' => 'Application',
                    'step' => ApplicationStep::SEEN,
                    'state' => [
                        'value' => 'in_progress',
                        'label' => 'Candidature en cours',
                    ],
                    'content' => 'Job 3 - Application 1',
                    'createdAt' => '2021-01-03T10:00:00+01:00',
                    'updatedAt' => '2021-01-04T10:00:00+01:00',
                    'favoriteAt' => null,
                    'jobPosting' => [
                        'title' => 'Ingénieur BI (F/H)',
                        'minAnnualSalary' => 30000,
                        'maxAnnualSalary' => 30000,
                        'minDailySalary' => null,
                        'maxDailySalary' => null,
                        'currency' => 'USD',
                        'contracts' => ['permanent'],
                        'duration' => null,
                        'renewable' => false,
                        'remoteMode' => RemoteMode::FULL,
                        'location' => [
                            'street' => null,
                            'locality' => 'Seyssinet-Pariset',
                            'postalCode' => '38170',
                            'adminLevel1' => 'Auvergne-Rhône-Alpes',
                            'adminLevel2' => 'Isère',
                            'country' => 'France',
                            'countryCode' => 'FR',
                            'latitude' => '45.1790768',
                            'longitude' => '5.6899617',
                            'key' => 'fr~auvergne-rhone-alpes~isere~seyssinet-pariset',
                            'label' => 'Seyssinet-Pariset, Auvergne-Rhône-Alpes',
                            'shortLabel' => 'Seyssinet-Pariset (38)',
                        ],
                        'startsAt' => '2021-04-28T00:00:00+02:00',
                        'company' => [
                            '@id' => '/companies/company-3',
                            '@type' => 'Company',
                            'id' => 3,
                            'name' => 'Company 3',
                            'slug' => 'company-3',
                            'logo' => [
                                'small' => 'https://api.freework.localhost/media/cache/resolve/company_logo_small/company-3-logo.jpg',
                                'medium' => 'https://api.freework.localhost/media/cache/resolve/company_logo_medium/company-3-logo.jpg',
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
                                '@id' => '/skills/3',
                                '@type' => 'Skill',
                                'id' => 3,
                                'name' => 'javascript',
                                'slug' => 'javascript',
                            ],
                        ],
                        'annualSalary' => "30k\u{a0}\$US",
                        'dailySalary' => null,
                        'applicationsCount' => 1,
                    ],
                    'company' => null,
                    'documents' => [
                        [
                            'document' => [
                                '@id' => '/user_documents/1',
                                '@type' => 'UserDocument',
                                'document' => getenv('AMAZON_S3_PREFIX') . '/test/users/documents/document1-cm.docx',
                                'resume' => true,
                                'defaultResume' => true,
                            ],
                        ],
                        [
                            'document' => [
                                '@id' => '/user_documents/2',
                                '@type' => 'UserDocument',
                                'document' => getenv('AMAZON_S3_PREFIX') . '/test/users/documents/document2-cm.docx',
                                'resume' => true,
                                'defaultResume' => false,
                            ],
                        ],
                    ],
                ],
                [
                    '@type' => 'Application',
                    'step' => ApplicationStep::SEEN,
                    'state' => [
                        'value' => 'in_progress',
                        'label' => 'Candidature en cours',
                    ],
                    'content' => 'Job 2 - Application 1',
                    'createdAt' => '2021-01-02T10:00:00+01:00',
                    'updatedAt' => '2021-01-03T10:00:00+01:00',
                    'favoriteAt' => '2021-03-01T23:45:00+01:00',
                    'seenAt' => '2021-01-02T11:30:00+01:00',
                    'jobPosting' => [
                        'title' => 'Responsable cybersécurité (sans management) (H/F)',
                        'minAnnualSalary' => 45000,
                        'maxAnnualSalary' => 45000,
                        'minDailySalary' => null,
                        'maxDailySalary' => null,
                        'currency' => 'USD',
                        'contracts' => ['permanent'],
                        'duration' => 3,
                        'renewable' => false,
                        'remoteMode' => RemoteMode::NONE,
                        'location' => [
                            'street' => null,
                            'locality' => 'Le Touquet-Paris-Plage',
                            'postalCode' => '62520',
                            'adminLevel1' => 'Hauts-de-France',
                            'adminLevel2' => 'Pas-de-Calais',
                            'country' => 'France',
                            'countryCode' => 'FR',
                            'latitude' => '50.5211202',
                            'longitude' => '1.5909325',
                            'key' => 'fr~hauts-de-france~pas-de-calais~le-touquet-paris-plage',
                            'label' => 'Le Touquet-Paris-Plage, Hauts-de-France',
                            'shortLabel' => 'Le Touquet-Paris-Plage (62)',
                        ],
                        'startsAt' => '2021-05-20T00:00:00+02:00',
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
                                '@id' => '/skills/4',
                                '@type' => 'Skill',
                                'id' => 4,
                                'name' => 'symfony',
                                'slug' => 'symfony',
                            ],
                            [
                                '@id' => '/skills/6',
                                '@type' => 'Skill',
                                'id' => 6,
                                'name' => 'laravel',
                                'slug' => 'laravel',
                            ],
                        ],
                        'annualSalary' => "45k\u{a0}\$US",
                        'dailySalary' => null,
                        'applicationsCount' => 2,
                    ],
                    'company' => null,
                    'documents' => [],
                ],
            ],
            'hydra:totalItems' => 3,
        ]);
    }

    public function testWithApplicationsOnCompanies(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('auguste.renoir@free-work.fr');
        $client->request('GET', '/users/8/applications');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Application',
            '@id' => '/users/8/applications',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@type' => 'Application',
                    'step' => ApplicationStep::RESUME,
                    'state' => [
                        'value' => 'in_progress',
                        'label' => 'Candidature en cours',
                    ],
                    'content' => 'Company 2 - Application 1',
                    'createdAt' => '2021-01-07T10:00:00+01:00',
                    'updatedAt' => '2021-01-08T10:00:00+01:00',
                    'favoriteAt' => null,
                    'jobPosting' => null,
                    'company' => [
                        '@id' => '/companies/company-2',
                        '@type' => 'Company',
                        'id' => 2,
                        'name' => 'Company 2',
                        'slug' => 'company-2',
                        'logo' => [
                            'small' => 'https://api.freework.localhost/media/cache/resolve/company_logo_small/company-2-logo.jpg',
                            'medium' => 'https://api.freework.localhost/media/cache/resolve/company_logo_medium/company-2-logo.jpg',
                        ],
                    ],
                    'documents' => [],
                ],
                [
                    '@type' => 'Application',
                    'step' => ApplicationStep::SEEN,
                    'state' => [
                        'value' => 'in_progress',
                        'label' => 'Candidature en cours',
                    ],
                    'content' => 'Company 1 - Application 1',
                    'createdAt' => '2021-01-06T10:00:00+01:00',
                    'updatedAt' => '2021-01-07T10:00:00+01:00',
                    'favoriteAt' => null,
                    'seenAt' => '2021-01-06T12:00:00+01:00',
                    'jobPosting' => null,
                    'company' => [
                        '@id' => '/companies/company-1',
                        '@type' => 'Company',
                        'id' => 1,
                        'name' => 'Company 1',
                        'logo' => [
                            'small' => 'https://api.freework.localhost/media/cache/resolve/company_logo_small/company-1-logo.jpg',
                            'medium' => 'https://api.freework.localhost/media/cache/resolve/company_logo_medium/company-1-logo.jpg',
                        ],
                    ],
                    'documents' => [],
                ],
            ],
            'hydra:totalItems' => 2,
        ]);
    }

    public function testWithState(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('vincent.van-gogh@free-work.fr');
        $client->request('GET', '/users/7/applications?state=unsuccessful');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Application',
            '@id' => '/users/7/applications',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@type' => 'Application',
                    'step' => ApplicationStep::KO,
                    'state' => [
                        'value' => 'unsuccessful',
                        'label' => 'Candidature non-retenue',
                    ],
                    'content' => 'Job 1 - Application 2',
                    'createdAt' => '2021-01-04T10:00:00+01:00',
                    'updatedAt' => '2021-01-05T10:00:00+01:00',
                    'favoriteAt' => null,
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
                        'location' => [
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
                    'documents' => [
                        [
                            'document' => [
                                '@id' => '/user_documents/4',
                                '@type' => 'UserDocument',
                                'document' => getenv('AMAZON_S3_PREFIX') . '/test/users/documents/document3-vvg.pdf',
                                'resume' => true,
                                'defaultResume' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'hydra:totalItems' => 1,
        ]);
    }
}
