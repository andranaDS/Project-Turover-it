<?php

namespace App\Core\Tests\Functional\Job;

use App\Tests\Functional\ApiTestCase;

class JobsGetTest extends ApiTestCase
{
    public function testWithoutFilter(): void
    {
        $client = self::createFreeWorkClient();
        $client->request('GET', '/jobs');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Job',
            '@id' => '/jobs',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase',
                    '@type' => 'Job',
                    'id' => 1,
                    'name' => 'Administrateur BDD',
                    'slug' => 'administrateur-bdd',
                    'availableForContribution' => true,
                    'nameForContribution' => 'Administrateur de base de donnée (oracle, sybase…)',
                    'nameForContributionSlug' => 'administrateur-de-base-de-donnee-oracle-sybase',
                    'availableForUser' => true,
                    'nameForUser' => 'Administrateur de base de données',
                    'nameForUserSlug' => 'administrateur-de-base-de-donnees',
                    'category' => [
                        '@id' => '/job_categories/1',
                        '@type' => 'JobCategory',
                        'id' => 1,
                        'name' => 'Data',
                        'slug' => 'data',
                    ],
                ],
                [
                    '@id' => '/jobs/administrateur-applicatif-erp-crm-sirh',
                    '@type' => 'Job',
                    'id' => 2,
                    'name' => 'Administrateur ERP',
                    'slug' => 'administrateur-erp',
                    'availableForContribution' => true,
                    'nameForContribution' => 'Administrateur applicatif (ERP, CRM, SIRH ...)',
                    'nameForContributionSlug' => 'administrateur-applicatif-erp-crm-sirh',
                    'availableForUser' => true,
                    'nameForUser' => 'Administrateur applicatif',
                    'nameForUserSlug' => 'administrateur-applicatif',
                    'category' => [
                        '@id' => '/job_categories/8',
                        '@type' => 'JobCategory',
                        'id' => 8,
                        'name' => 'Production, Système et Réseau , Support',
                        'slug' => 'production-systeme-et-reseau-support',
                    ],
                ],
            ],
            'hydra:totalItems' => 183,
            'hydra:view' => [
                '@id' => '/jobs?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/jobs?page=1',
                'hydra:last' => '/jobs?page=92',
                'hydra:next' => '/jobs?page=2',
            ],
            'hydra:search' => [
                '@type' => 'hydra:IriTemplate',
                'hydra:template' => '/jobs{?name,nameForContribution,nameForUser,availableForContribution,availableForUser,properties[]}',
                'hydra:variableRepresentation' => 'BasicRepresentation',
                'hydra:mapping' => [
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'name',
                        'property' => 'name',
                        'required' => false,
                    ],
                ],
            ],
        ]);
    }

    public function testWithNameFilterAndResults(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/jobs?name=developpeur');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Job',
            '@id' => '/jobs',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    'name' => 'API Développeur',
                    'slug' => 'api-developpeur',
                ],
                [
                    'name' => 'Développeur',
                    'slug' => 'developpeur',
                ],
            ],
            'hydra:totalItems' => 19,
        ]);
    }

    public function testWithNameFilterAndWithoutResults(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/jobs?name=wrong-name');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Job',
            '@id' => '/jobs',
            '@type' => 'hydra:Collection',
            'hydra:member' => [],
            'hydra:totalItems' => 0,
        ]);
    }

    public function testWithAvailableForUserFilter(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/jobs?availableForUser=true');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Job',
            '@id' => '/jobs',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase',
                    '@type' => 'Job',
                    'id' => 1,
                    'name' => 'Administrateur BDD',
                    'slug' => 'administrateur-bdd',
                    'availableForContribution' => true,
                    'nameForContribution' => 'Administrateur de base de donnée (oracle, sybase…)',
                    'nameForContributionSlug' => 'administrateur-de-base-de-donnee-oracle-sybase',
                    'availableForUser' => true,
                    'nameForUser' => 'Administrateur de base de données',
                    'nameForUserSlug' => 'administrateur-de-base-de-donnees',
                    'category' => [
                        '@id' => '/job_categories/1',
                        '@type' => 'JobCategory',
                        'id' => 1,
                        'name' => 'Data',
                        'slug' => 'data',
                    ],
                ],
                [
                    '@id' => '/jobs/administrateur-applicatif-erp-crm-sirh',
                    '@type' => 'Job',
                    'id' => 2,
                    'name' => 'Administrateur ERP',
                    'slug' => 'administrateur-erp',
                    'availableForContribution' => true,
                    'nameForContribution' => 'Administrateur applicatif (ERP, CRM, SIRH ...)',
                    'nameForContributionSlug' => 'administrateur-applicatif-erp-crm-sirh',
                    'availableForUser' => true,
                    'nameForUser' => 'Administrateur applicatif',
                    'nameForUserSlug' => 'administrateur-applicatif',
                    'category' => [
                        '@id' => '/job_categories/8',
                        '@type' => 'JobCategory',
                        'id' => 8,
                        'name' => 'Production, Système et Réseau , Support',
                        'slug' => 'production-systeme-et-reseau-support',
                    ],
                ],
            ],
            'hydra:totalItems' => 114,
        ]);
    }

    public function testWithNameForUserFilter(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/jobs?nameForUser=oracle&availableForUser=true');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Job',
            '@id' => '/jobs',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/jobs/administrateur-oracle',
                    '@type' => 'Job',
                    'id' => 4,
                    'name' => 'Administrateur Oracle',
                    'slug' => 'administrateur-oracle',
                    'availableForContribution' => false,
                    'nameForContribution' => 'Administrateur Oracle',
                    'nameForContributionSlug' => 'administrateur-oracle',
                    'availableForUser' => true,
                    'nameForUser' => 'Administrateur oracle',
                    'nameForUserSlug' => 'administrateur-oracle',
                    'category' => null,
                ],
                [
                    '@id' => '/jobs/consultant-oracle',
                    '@type' => 'Job',
                    'id' => 64,
                    'name' => 'Consultant Oracle',
                    'slug' => 'consultant-oracle',
                    'availableForContribution' => false,
                    'nameForContribution' => 'Consultant Oracle',
                    'nameForContributionSlug' => 'consultant-oracle',
                    'availableForUser' => true,
                    'nameForUser' => 'Consultant oracle',
                    'nameForUserSlug' => 'consultant-oracle',
                    'category' => null,
                ],
            ],
            'hydra:totalItems' => 2,
        ]);
    }

    public function testWithAvailableForContributionFilter(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/jobs?availableForContribution=true');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Job',
            '@id' => '/jobs',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase',
                    '@type' => 'Job',
                    'id' => 1,
                    'name' => 'Administrateur BDD',
                    'slug' => 'administrateur-bdd',
                    'availableForContribution' => true,
                    'nameForContribution' => 'Administrateur de base de donnée (oracle, sybase…)',
                    'nameForContributionSlug' => 'administrateur-de-base-de-donnee-oracle-sybase',
                    'availableForUser' => true,
                    'nameForUser' => 'Administrateur de base de données',
                    'nameForUserSlug' => 'administrateur-de-base-de-donnees',
                    'category' => [
                        '@id' => '/job_categories/1',
                        '@type' => 'JobCategory',
                        'id' => 1,
                        'name' => 'Data',
                        'slug' => 'data',
                    ],
                ],
                [
                    '@id' => '/jobs/administrateur-applicatif-erp-crm-sirh',
                    '@type' => 'Job',
                    'id' => 2,
                    'name' => 'Administrateur ERP',
                    'slug' => 'administrateur-erp',
                    'availableForContribution' => true,
                    'nameForContribution' => 'Administrateur applicatif (ERP, CRM, SIRH ...)',
                    'nameForContributionSlug' => 'administrateur-applicatif-erp-crm-sirh',
                    'availableForUser' => true,
                    'nameForUser' => 'Administrateur applicatif',
                    'nameForUserSlug' => 'administrateur-applicatif',
                    'category' => [
                        '@id' => '/job_categories/8',
                        '@type' => 'JobCategory',
                        'id' => 8,
                        'name' => 'Production, Système et Réseau , Support',
                        'slug' => 'production-systeme-et-reseau-support',
                    ],
                ],
            ],
            'hydra:totalItems' => 107,
        ]);
    }

    public function testWithNameForContributionFilter(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/jobs?nameForContribution=oracle&availableForContribution=true');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Job',
            '@id' => '/jobs',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase',
                    '@type' => 'Job',
                    'id' => 1,
                    'name' => 'Administrateur BDD',
                    'slug' => 'administrateur-bdd',
                    'availableForContribution' => true,
                    'nameForContribution' => 'Administrateur de base de donnée (oracle, sybase…)',
                    'nameForContributionSlug' => 'administrateur-de-base-de-donnee-oracle-sybase',
                    'availableForUser' => true,
                    'nameForUser' => 'Administrateur de base de données',
                    'nameForUserSlug' => 'administrateur-de-base-de-donnees',
                    'category' => [
                        '@id' => '/job_categories/1',
                        '@type' => 'JobCategory',
                        'id' => 1,
                        'name' => 'Data',
                        'slug' => 'data',
                    ],
                ],
                [
                    '@id' => '/jobs/consultant-erp-ms-dynamics-oracle-sage-sap',
                    '@type' => 'Job',
                    'id' => 57,
                    'name' => 'Consultant ERP-Systems',
                    'slug' => 'consultant-erp-systems',
                    'availableForContribution' => true,
                    'nameForContribution' => 'Consultant ERP (MS dynamics, oracle, sage, sap ...)',
                    'nameForContributionSlug' => 'consultant-erp-ms-dynamics-oracle-sage-sap',
                    'availableForUser' => true,
                    'nameForUser' => 'Consultant erp-systems',
                    'nameForUserSlug' => 'consultant-erp-systems',
                    'category' => [
                        '@id' => '/job_categories/4',
                        '@type' => 'JobCategory',
                        'id' => 4,
                        'name' => 'Expert',
                        'slug' => 'expert',
                    ],
                ],
            ],
            'hydra:totalItems' => 4,
        ]);
    }

    public function testWithPropertyFilter(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/jobs?properties[]=name&properties[]=slug');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains([
            '@context' => '/contexts/Job',
            '@id' => '/jobs',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase',
                    '@type' => 'Job',
                    'name' => 'Administrateur BDD',
                    'slug' => 'administrateur-bdd',
                ],
                [
                    '@id' => '/jobs/administrateur-applicatif-erp-crm-sirh',
                    '@type' => 'Job',
                    'name' => 'Administrateur ERP',
                    'slug' => 'administrateur-erp',
                ],
            ],
            'hydra:totalItems' => 183,
        ]);
    }
}
