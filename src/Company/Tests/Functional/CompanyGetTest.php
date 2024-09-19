<?php

namespace App\Company\Tests\Functional;

use App\Company\Entity\Company;
use App\Tests\Functional\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;

class CompanyGetTest extends ApiTestCase
{
    public function testNotFound(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/companies/zidane-corporation');

        self::assertResponseStatusCodeSame(404);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testFound(): void
    {
        $client = static::createTurnoverAuthenticatedClient('eddard.stark@got.com');

        if (null === $container = $client->getContainer()) {
            throw new \RuntimeException('Container is null');
        }

        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();

        // 1 - before
        $company = $em->find(Company::class, 2);
        self::assertNull($company->getFeaturesUsage()->getCompanyLogAt());

        $client->request('GET', '/companies/company-1');
        self::assertResponseIsSuccessful();

        // 2 - after
        $company = $em->find(Company::class, 2);
        self::assertNotNull($company->getFeaturesUsage()->getCompanyLogAt());

        $client = static::createFreeWorkClient();
        $client->request('GET', '/companies/company-1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Company',
            '@id' => '/companies/company-1',
            '@type' => 'Company',
            'id' => 1,
            'name' => 'Company 1',
            'slug' => 'company-1',
            'excerpt' => 'Company 1 // Excerpt',
            'description' => 'Company 1 // Description',
            'annualRevenue' => '100k',
            'directoryFreeWork' => true,
            'directoryTurnover' => true,
            'data' => [
                'jobPostingsTotalCount' => 12,
                'jobPostingsFreeTotalCount' => 0,
                'jobPostingsWorkTotalCount' => 12,
                'jobPostingsPublishedCount' => 12,
                'jobPostingsFreePublishedCount' => 0,
                'jobPostingsWorkPublishedCount' => 12,
                'jobPostingsIntercontractTotalCount' => 0,
                'jobPostingsIntercontractPublishedCount' => 0,
                'usersCount' => 2,
                'usersVisibleCount' => 1,
            ],
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
            'pictures' => [
                [
                    '@id' => '/company_pictures/1',
                    '@type' => 'CompanyPicture',
                    'id' => 1,
                    'image' => [
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_medium/company-1-picture-2.jpg',
                        'large' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_large/company-1-picture-2.jpg',
                    ],
                ],
                [
                    '@id' => '/company_pictures/2',
                    '@type' => 'CompanyPicture',
                    'id' => 2,
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
            'softSkills' => [
                [
                    '@id' => '/soft_skills/1',
                    '@type' => 'SoftSkill',
                    'id' => 1,
                    'name' => 'SoftSkill 1',
                    'slug' => 'softskill-1',
                ],
                [
                    '@id' => '/soft_skills/2',
                    '@type' => 'SoftSkill',
                    'id' => 2,
                    'name' => 'SoftSkill 2',
                    'slug' => 'softskill-2',
                ],
            ],
        ]);
    }
}
