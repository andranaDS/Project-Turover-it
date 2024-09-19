<?php

namespace App\Core\Tests\Functional\JobCategory;

use App\Tests\Functional\ApiTestCase;

class JobCategoryGetTest extends ApiTestCase
{
    public function testFound(): void
    {
        $client = self::createFreeWorkClient();
        $client->request('GET', '/job_categories/1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/JobCategory',
            '@id' => '/job_categories/1',
            '@type' => 'JobCategory',
            'id' => 1,
            'name' => 'Data',
            'slug' => 'data',
            'jobs' => [
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
                ],
                [
                    '@id' => '/jobs/architecte-de-base-de-donnees',
                    '@type' => 'Job',
                    'id' => 25,
                    'name' => 'Architecte Base de Données',
                    'slug' => 'architecte-base-de-donnees',
                    'availableForContribution' => true,
                    'nameForContribution' => 'Architecte de base de données',
                    'nameForContributionSlug' => 'architecte-de-base-de-donnees',
                    'availableForUser' => true,
                    'nameForUser' => 'Architecte de base de données',
                    'nameForUserSlug' => 'architecte-de-base-de-donnees',
                ],
                [
                    '@id' => '/jobs/concepteur-de-base-de-donnee',
                    '@type' => 'Job',
                    'id' => 48,
                    'name' => 'Concepteur BD',
                    'slug' => 'concepteur-bd',
                    'availableForContribution' => true,
                    'nameForContribution' => 'Concepteur de base de donnée',
                    'nameForContributionSlug' => 'concepteur-de-base-de-donnee',
                    'availableForUser' => true,
                    'nameForUser' => 'Concepteur de base de donnée',
                    'nameForUserSlug' => 'concepteur-de-base-de-donnee',
                ],
                [
                    '@id' => '/jobs/consultant-decisionnel-bi-powerbi-sas-tableau',
                    '@type' => 'Job',
                    'id' => 54,
                    'name' => 'Consultant BI',
                    'slug' => 'consultant-bi',
                    'availableForContribution' => true,
                    'nameForContribution' => 'Consultant décisionnel/BI (PowerBI, SAS, Tableau ...)',
                    'nameForContributionSlug' => 'consultant-decisionnel-bi-powerbi-sas-tableau',
                    'availableForUser' => true,
                    'nameForUser' => 'Consultant décisionnel/BI',
                    'nameForUserSlug' => 'consultant-decisionnel-bi',
                ],
                [
                    '@id' => '/jobs/directeur-de-la-data-cdo',
                    '@type' => 'Job',
                    'id' => 72,
                    'name' => 'Data Manager',
                    'slug' => 'data-manager',
                    'availableForContribution' => true,
                    'nameForContribution' => 'Directeur de la data (CDO)',
                    'nameForContributionSlug' => 'directeur-de-la-data-cdo',
                    'availableForUser' => true,
                    'nameForUser' => 'Directeur de la data',
                    'nameForUserSlug' => 'directeur-de-la-data',
                ],
                [
                    '@id' => '/jobs/data-scientist',
                    '@type' => 'Job',
                    'id' => 73,
                    'name' => 'Data Scientist',
                    'slug' => 'data-scientist',
                    'availableForContribution' => true,
                    'nameForContribution' => 'Data scientist',
                    'nameForContributionSlug' => 'data-scientist',
                    'availableForUser' => true,
                    'nameForUser' => 'Data scientist',
                    'nameForUserSlug' => 'data-scientist',
                ],
                [
                    '@id' => '/jobs/data-engineer',
                    '@type' => 'Job',
                    'id' => 114,
                    'name' => 'Ingénieur Data',
                    'slug' => 'ingenieur-data',
                    'availableForContribution' => true,
                    'nameForContribution' => 'Data engineer',
                    'nameForContributionSlug' => 'data-engineer',
                    'availableForUser' => true,
                    'nameForUser' => 'Data engineer',
                    'nameForUserSlug' => 'data-engineer',
                ],
                [
                    '@id' => '/jobs/developpeur-ia-machine-learning',
                    '@type' => 'Job',
                    'id' => 117,
                    'name' => 'Ingénieur de Recherche Machine Learning',
                    'slug' => 'ingenieur-de-recherche-machine-learning',
                    'availableForContribution' => true,
                    'nameForContribution' => 'Développeur IA/machine learning',
                    'nameForContributionSlug' => 'developpeur-ia-machine-learning',
                    'availableForUser' => true,
                    'nameForUser' => 'Développeur IA/machine learning',
                    'nameForUserSlug' => 'developpeur-ia-machine-learning',
                ],
            ],
        ]);
    }

    public function testNotFound(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/job_categories/not-found');

        self::assertResponseStatusCodeSame(404);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}
