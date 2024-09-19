<?php

namespace App\Company\Tests\Functional\FreeWork\Company;

use App\Tests\Functional\ApiTestCase;

class CompaniesBlacklistsGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/companies/blacklists');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/companies/blacklists');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('GET', '/companies/blacklists');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testData(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('pablo.picasso@free-work.fr');
        $client->request('GET', '/companies/blacklists');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains([
            '@context' => '/contexts/Company',
            '@id' => '/companies',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/companies/company-5',
                    '@type' => 'Company',
                    'id' => 5,
                    'name' => 'Company 5',
                    'slug' => 'company-5',
                    'businessActivity' => [
                        '@id' => '/company_business_activities/3',
                        '@type' => 'CompanyBusinessActivity',
                        'id' => 3,
                        'name' => 'Business activity 3',
                        'slug' => 'business-activity-3',
                    ],
                    'size' => [
                        'value' => 'more_than_1000_employees',
                        'label' => '> 1 000',
                    ],
                    'websiteUrl' => 'https://www.company-5.com',
                    'linkedInUrl' => 'https://www.linkedin.com/company-5',
                    'twitterUrl' => 'https://www.twitter.com/company-5',
                    'facebookUrl' => null,
                    'location' => [
                        'street' => null,
                        'locality' => null,
                        'postalCode' => null,
                        'adminLevel1' => 'Île-de-France',
                        'adminLevel2' => null,
                        'country' => 'France',
                        'countryCode' => 'FR',
                        'latitude' => '48.6443057',
                        'longitude' => '2.7537863',
                        'key' => 'fr~ile-de-france~~',
                        'label' => 'Île-de-France, France',
                        'shortLabel' => 'Île-de-France',
                    ],
                    'logo' => [
                        'small' => 'https://api.freework.localhost/media/cache/resolve/company_logo_small/company-5-logo.jpg',
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/company_logo_medium/company-5-logo.jpg',
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
                    'creationYear' => 1992,
                    'pictures' => [
                        [
                            '@id' => '/company_pictures/3',
                            '@type' => 'CompanyPicture',
                            'image' => [
                                'medium' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_medium/company-5-picture-2.jpg',
                                'large' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_large/company-5-picture-2.jpg',
                            ],
                        ],
                    ],
                    'coverPicture' => [
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_medium/company-5-picture-1.jpg',
                        'large' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_large/company-5-picture-1.jpg',
                    ],
                    'excerpt' => 'Company 5 // Excerpt',
                    'description' => 'Company 5 // Description',
                ],
                [
                    '@id' => '/companies/company-4',
                    '@type' => 'Company',
                    'id' => 4,
                    'name' => 'Company 4',
                    'slug' => 'company-4',
                    'businessActivity' => [
                        '@id' => '/company_business_activities/1',
                        '@type' => 'CompanyBusinessActivity',
                        'id' => 1,
                        'name' => 'Business activity 1',
                        'slug' => 'business-activity-1',
                    ],
                    'size' => [
                        'value' => 'less_than_20_employees',
                        'label' => '< 20',
                    ],
                    'websiteUrl' => 'https://www.company-4.com',
                    'linkedInUrl' => 'https://www.linkedin.com/company-4',
                    'twitterUrl' => 'null',
                    'facebookUrl' => 'https://www.facebook.com/company-4',
                    'location' => [
                        'street' => null,
                        'locality' => null,
                        'postalCode' => null,
                        'adminLevel1' => 'Île-de-France',
                        'adminLevel2' => null,
                        'country' => 'France',
                        'countryCode' => 'FR',
                        'latitude' => '48.6443057',
                        'longitude' => '2.7537863',
                        'key' => 'fr~ile-de-france~~',
                        'label' => 'Île-de-France, France',
                        'shortLabel' => 'Île-de-France',
                    ],
                    'logo' => [
                        'small' => 'https://api.freework.localhost/media/cache/resolve/company_logo_small/company-4-logo.jpg',
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/company_logo_medium/company-4-logo.jpg',
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
                    'creationYear' => 1982,
                    'pictures' => [],
                    'coverPicture' => null,
                    'excerpt' => 'Company 4 // Excerpt',
                    'description' => 'Company 4 // Description',
                ],
            ],
            'hydra:totalItems' => 3,
            'hydra:view' => [
                '@id' => '/companies/blacklists?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/companies/blacklists?page=1',
                'hydra:last' => '/companies/blacklists?page=2',
                'hydra:next' => '/companies/blacklists?page=2',
            ],
        ]);
    }
}
