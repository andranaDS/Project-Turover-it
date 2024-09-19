<?php

namespace App\Company\Tests\Functional\Turnover\Company;

use App\Company\Entity\Company;
use App\Tests\Functional\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\ByteString;

class CompanyPatchDirectoryTest extends ApiTestCase
{
    public static function provideValidCases(): iterable
    {
        return [
            ['/companies/company-1/directory'],
            ['/companies/mine/directory'],
        ];
    }

    /**
     * @dataProvider provideValidCases
     */
    public function testValidCases(string $path): void
    {
        $client = static::createTurnoverAuthenticatedClient();

        $client->request('PATCH', $path, [
            'json' => [
                'name' => 'Company 1 // Name Patch',
                'baseline' => 'Company 1 // Baseline Patch',
                'description' => 'Company 1 // Description Patch',
                'locationKey' => 'fr~nouvelle-aquitaine~~bordeaux',
                'creationYear' => 1910,
                'annualRevenue' => '150k',
                'size' => 'employees_20_99',
                'businessActivity' => '/company_business_activities/3',
                'directoryFreeWork' => false,
                'directoryTurnover' => false,
                'skills' => [
                    '/skills/1',
                    '/skills/3',
                ],
                'softSkills' => [
                    '/soft_skills/1',
                    '/soft_skills/3',
                ],
            ],
        ]);

        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Company',
            '@id' => '/companies/company-1-name-patch',
            '@type' => 'Company',
            'name' => 'Company 1 // Name Patch',
            'baseline' => 'Company 1 // Baseline Patch',
            'description' => 'Company 1 // Description Patch',
            'location' => [
                'street' => null,
                'locality' => 'Bordeaux',
                'postalCode' => '33000',
                'adminLevel1' => 'Nouvelle-Aquitaine',
                'adminLevel2' => null,
                'country' => 'France',
                'countryCode' => 'FR',
                'latitude' => '44.8412250',
                'longitude' => '-0.5800364',
                'key' => 'fr~nouvelle-aquitaine~~bordeaux',
                'label' => 'Bordeaux, Nouvelle-Aquitaine',
                'shortLabel' => 'Bordeaux (33)',
            ],
            'creationYear' => 1910,
            'annualRevenue' => '150k',
            'size' => [
                'value' => 'employees_20_99',
                'label' => '20 - 99',
            ],
            'businessActivity' => [
                '@type' => 'CompanyBusinessActivity',
                'id' => 3,
                'name' => 'Business activity 3',
            ],
            'directoryFreeWork' => false,
            'directoryTurnover' => false,
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
            'softSkills' => [
                [
                    '@id' => '/soft_skills/1',
                    '@type' => 'SoftSkill',
                    'id' => 1,
                    'name' => 'SoftSkill 1',
                    'slug' => 'softskill-1',
                ],
                [
                    '@id' => '/soft_skills/3',
                    '@type' => 'SoftSkill',
                    'id' => 3,
                    'name' => 'SoftSkill 3',
                    'slug' => 'softskill-3',
                ],
            ],
        ]);
    }

    /**
     * @dataProvider provideValidCases
     */
    public function testWithCompanyMember(string $path): void
    {
        $client = static::createTurnoverAuthenticatedClient('jesse.pinkman@breaking-bad.com');

        $client->request('PATCH', $path, [
            'json' => [
                'name' => 'Company 1 // Name Patch',
                'baseline' => 'Company 1 // Baseline Patch',
                'description' => 'Company 1 // Description Patch',
                'locationKey' => 'fr~nouvelle-aquitaine~~bordeaux',
                'creationYear' => 1910,
                'annualRevenue' => '150k',
                'size' => 'employees_20_99',
                'businessActivity' => '/company_business_activities/3',
                'directoryFreeWork' => false,
                'directoryTurnover' => false,
                'skills' => [
                    '/skills/1',
                    '/skills/3',
                ],
                'softSkills' => [
                    '/soft_skills/1',
                    '/soft_skills/3',
                ],
            ],
        ]);

        self::assertResponseStatusCodeSame(200);
    }

    /**
     * @dataProvider provideValidCases
     */
    public function testNotLoggedCases(string $path): void
    {
        $client = static::createTurnoverClient();
        $client->request('PATCH', $path, [
            'json' => [
                'name' => 'Company 1 // Name Patch',
                'baseline' => 'Company 1 // Baseline Patch',
                'description' => 'Company 1 // Description Patch',
                'locationKey' => 'fr~nouvelle-aquitaine~~bordeaux',
                'creationYear' => 1910,
                'annualRevenue' => '150k',
                'size' => 'employees_20_99',
                'businessActivity' => '/company_business_activities/3',
                'directoryFreeWork' => false,
                'directoryTurnover' => false,
                'skills' => [
                    '/skills/1',
                    '/skills/3',
                ],
                'softSkills' => [
                    '/soft_skills/1',
                    '/soft_skills/3',
                ],
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedOnWrongCompany(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('PATCH', '/companies/company-2/directory', [
            'json' => [
                'name' => 'Company 1 // Name Patch',
                'baseline' => 'Company 1 // Baseline Patch',
                'description' => 'Company 1 // Description Patch',
                'locationKey' => 'fr~nouvelle-aquitaine~~bordeaux',
                'creationYear' => 1910,
                'annualRevenue' => '150k',
                'size' => 'employees_20_99',
                'businessActivity' => '/company_business_activities/3',
                'directoryFreeWork' => false,
                'directoryTurnover' => false,
                'skills' => [
                    '/skills/1',
                    '/skills/3',
                ],
                'softSkills' => [
                    '/soft_skills/1',
                    '/soft_skills/3',
                ],
            ],
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    public static function provideInvalidCases(): iterable
    {
        return [
            'empty' => [
                [
                    'name' => '',
                    'baseline' => '',
                    'description' => '',
                    'locationKey' => '',
                    'creationYear' => null,
                    'annualRevenue' => '',
                    'size' => null,
                    'businessActivity' => null,
                    'directoryFreeWork' => false,
                    'directoryTurnover' => false,
                    'skills' => [],
                    'softSkills' => [],
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'name',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'description',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                    ],
                ],
            ],
            'length' => [
                [
                    'name' => ByteString::fromRandom(256),
                    'baseline' => ByteString::fromRandom(256),
                    'description' => ByteString::fromRandom(256),
                    'locationKey' => ByteString::fromRandom(256),
                    'creationYear' => 99999999999,
                    'annualRevenue' => ByteString::fromRandom(256),
                    'size' => ByteString::fromRandom(256),
                    'businessActivity' => '/company_business_activities/1',
                    'directoryFreeWork' => false,
                    'directoryTurnover' => false,
                    'skills' => [],
                    'softSkills' => [],
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'name',
                            'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 255 caractères.',
                        ],
                        [
                            'propertyPath' => 'size',
                            'message' => 'Cette valeur doit être l\'un des choix proposés.',
                        ],
                        [
                            'propertyPath' => 'creationYear',
                            'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 4 caractères.',
                        ],
                        [
                            'propertyPath' => 'baseline',
                            'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 100 caractères.',
                        ],
                    ],
                ],
            ],
        ];
    }

    public static function provideDirectoryTurnoverCases(): iterable
    {
        return [
            ['/companies/company-2/directory'],
            ['/companies/mine/directory'],
        ];
    }

    /**
     * @dataProvider provideDirectoryTurnoverCases
     */
    public function testDirectoryTurnoverCases(string $path): void
    {
        $client = static::createTurnoverAuthenticatedClient('eddard.stark@got.com');

        if (null === $container = $client->getContainer()) {
            throw new \RuntimeException('Container is null');
        }

        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();

        // 1 - before
        $company = $em->find(Company::class, 2);
        self::assertFalse($company->isDirectoryTurnover());
        self::assertNull($company->getFeaturesUsage()->getCompanyPublishAt());

        $client->request('PATCH', $path, [
            'json' => [
                'directoryTurnover' => true,
            ],
        ]);

        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Company',
            '@id' => '/companies/company-2',
            'directoryTurnover' => true,
        ]);

        // 2 - after
        $company = $em->find(Company::class, 2);
        self::assertTrue($company->isDirectoryTurnover());
        self::assertNotNull($company->getFeaturesUsage()->getCompanyPublishAt());
    }

    /**
     * @dataProvider provideInvalidCases
     */
    public function testInvalidCases(array $payload, array $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('PATCH', '/companies/company-1/directory', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains($expected);
    }
}
