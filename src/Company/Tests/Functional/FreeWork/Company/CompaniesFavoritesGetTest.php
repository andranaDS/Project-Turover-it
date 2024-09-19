<?php

namespace App\Company\Tests\Functional\FreeWork\Company;

use App\Tests\Functional\ApiTestCase;

class CompaniesFavoritesGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/companies/favorites');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/companies/favorites');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('GET', '/companies/favorites');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testData(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $a = $client->request('GET', '/companies/favorites');

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
                    '@id' => '/companies/company-3',
                    '@type' => 'Company',
                    'id' => 3,
                    'name' => 'Company 3',
                    'slug' => 'company-3',
                    'businessActivity' => [
                        '@id' => '/company_business_activities/2',
                        '@type' => 'CompanyBusinessActivity',
                        'id' => 2,
                        'name' => 'Business activity 2',
                        'slug' => 'business-activity-2',
                    ],
                    'size' => [
                        'value' => 'less_than_20_employees',
                        'label' => '< 20',
                    ],
                    'websiteUrl' => 'https://www.company-3.com',
                    'linkedInUrl' => null,
                    'twitterUrl' => 'https://www.twitter.com/company-3',
                    'facebookUrl' => 'https://www.facebook.com/company-3',
                    'location' => [
                        'street' => null,
                        'locality' => 'Lyon',
                        'postalCode' => null,
                        'adminLevel1' => 'Auvergne-Rhône-Alpes',
                        'adminLevel2' => 'Métropole de Lyon',
                        'country' => 'France',
                        'countryCode' => 'FR',
                        'latitude' => '45.7578137',
                        'longitude' => '4.8320114',
                        'key' => 'fr~auvergne-rhone-alpes~metropole-de-lyon~lyon',
                        'label' => 'Lyon, Auvergne-Rhône-Alpes',
                        'shortLabel' => 'Lyon',
                    ],
                    'logo' => [
                        'small' => 'https://api.freework.localhost/media/cache/resolve/company_logo_small/company-3-logo.jpg',
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/company_logo_medium/company-3-logo.jpg',
                    ],
                    'skills' => [
                        [
                            '@id' => '/skills/3',
                            '@type' => 'Skill',
                            'id' => 3,
                            'name' => 'javascript',
                            'slug' => 'javascript',
                        ],
                    ],
                    'creationYear' => 2018,
                    'pictures' => [],
                    'coverPicture' => null,
                    'excerpt' => 'Company 3 // Excerpt',
                    'description' => 'Company 3 // Description',
                ],
                [
                    '@id' => '/companies/company-1',
                    '@type' => 'Company',
                    'id' => 1,
                    'name' => 'Company 1',
                    'slug' => 'company-1',
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
                    'websiteUrl' => 'https://www.company-1.com',
                    'linkedInUrl' => 'https://www.linkedin.com/company-1',
                    'twitterUrl' => 'https://www.twitter.com/company-1',
                    'facebookUrl' => 'https://www.facebook.com/company-1',
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
                    'logo' => [
                        'small' => 'https://api.freework.localhost/media/cache/resolve/company_logo_small/company-1-logo.jpg',
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/company_logo_medium/company-1-logo.jpg',
                    ],
                    'skills' => [],
                    'creationYear' => 1904,
                    'directoryFreeWork' => true,
                    'pictures' => [
                        [
                            '@id' => '/company_pictures/1',
                            '@type' => 'CompanyPicture',
                            'image' => [
                                'medium' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_medium/company-1-picture-2.jpg',
                                'large' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_large/company-1-picture-2.jpg',
                            ],
                        ],
                        [
                            '@id' => '/company_pictures/2',
                            '@type' => 'CompanyPicture',
                            'image' => [
                                'medium' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_medium/company-1-picture-3.jpg',
                                'large' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_large/company-1-picture-3.jpg',
                            ],
                        ],
                    ],
                    'coverPicture' => [
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_medium/company-1-picture-1.jpg',
                        'large' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_large/company-1-picture-1.jpg',
                    ],
                    'excerpt' => 'Company 1 // Excerpt',
                    'description' => 'Company 1 // Description',
                ],
            ],
            'hydra:totalItems' => 3,
        ]);
    }
}
