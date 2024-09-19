<?php

namespace App\JobPosting\Tests\Functional\FreeWork\JobPosting;

use App\JobPosting\Enum\Contract;
use App\JobPosting\Enum\ExperienceLevel;
use App\JobPosting\Enum\RemoteMode;
use App\Tests\Functional\ApiTestCase;

class JobPostingsGetTest extends ApiTestCase
{
    public function testWithoutFilter(): void
    {
        self::synchronizeElasticsearch();

        $client = static::createFreeWorkClient();
        $client->request('GET', '/job_postings');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertMatchesJsonSchema([
            'hydra:member' => [
                '@context' => '/contexts/JobPosting',
                '@id' => '/job_postings/82',
                '@type' => 'JobPosting',
                'id' => '82',
                'title' => 'Chef de projet CRM Dynamics (F/H) - 92',
                'slug' => 'chef-de-projet-crm-dynamics-f-h-92',
                'description' => '',
                'experienceLevel' => ExperienceLevel::JUNIOR,
                'minAnnualSalary' => 28000,
                'maxAnnualSalary' => null,
                'minDailySalary' => null,
                'maxDailySalary' => null,
                'currency' => 'EUR',
                'contracts' => [Contract::PERMANENT],
                'duration' => 4,
            ],
        ]);
    }

    public function testWithSearchKeywordsFilter(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/job_postings?searchKeywords=Company+1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains([
            '@context' => '/contexts/JobPosting',
            'hydra:member' => [
                [
                    'company' => [
                        '@type' => 'Company',
                        'name' => 'Company 1',
                    ],
                ],
            ],
        ]);
    }

    public function testWithContractsFilter(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/job_postings?contracts=contractor');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains([
            '@context' => '/contexts/JobPosting',
            '@id' => '/job_postings',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 2,
            'hydra:member' => [],
        ]);

        $client->request('GET', '/job_postings?contracts=contractor,internship');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains([
            '@context' => '/contexts/JobPosting',
            '@id' => '/job_postings',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 4,
            'hydra:member' => [
                [
                    '@id' => '/job_postings/38',
                    '@type' => 'JobPosting',
                    'id' => 38,
                    'title' => 'Consultant en Poste de travail F/H',
                    'slug' => 'consultant-en-poste-de-travail-f-h',
                    'experienceLevel' => 'intermediate',
                    'minAnnualSalary' => 50000,
                    'maxAnnualSalary' => 50000,
                    'minDailySalary' => 450,
                    'maxDailySalary' => 450,
                    'currency' => 'GBP',
                    'contracts' => [
                        'permanent',
                    ],
                    'duration' => 5,
                    'renewable' => false,
                    'remoteMode' => 'partial',
                    'applicationType' => 'turnover',
                    'applicationContact' => null,
                    'applicationUrl' => null,
                    'applicationsCount' => 0,
                    'job' => [
                        'id' => 53,
                        'name' => 'Consultant',
                        'slug' => 'consultant',
                    ],
                    'location' => [
                        '@type' => 'Location',
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
                    'startsAt' => '2021-11-08T00:00:00+01:00',
                    'reference' => null,
                    'company' => [
                        '@id' => '/companies/company-2',
                        '@type' => 'Company',
                        'id' => 2,
                        'name' => 'Company 2',
                        'slug' => 'company-2',
                        'description' => 'Company 2 // Description',
                        'directoryFreeWork' => false,
                        'logo' => [
                            'small' => 'https://api.freework.localhost/media/cache/resolve/company_logo_small/company-2-logo.jpg',
                            'medium' => 'https://api.freework.localhost/media/cache/resolve/company_logo_medium/company-2-logo.jpg',
                        ],
                        'coverPicture' => null,
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
                    'publishedAt' => '2021-04-20T17:25:12+02:00',
                    'published' => true,
                    'annualSalary' => "50k\u{a0}£GB",
                    'dailySalary' => "450\u{a0}£GB",
                ],
            ],
        ]);
    }

    public function testWithSalaryFilter(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/job_postings?minAnnualSalary=60000');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains([
            '@context' => '/contexts/JobPosting',
            '@id' => '/job_postings',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/job_postings/51',
                    '@type' => 'JobPosting',
                    'id' => 51,
                    'title' => 'Développeur Java 8 ans Exp - IDF - (H/F)',
                    'slug' => 'developpeur-java-8-ans-exp-idf-h-f',
                    'description' => "Descriptif du poste : \r\n\r\nEn tant que développeur au sein d&rsquo;une équipe d&rsquo;une quinzaine personnes, vous serez responsable :\r\n\r\n   du développement\r\n   du packaging\r\n   des tests unitaires\r\n   des tests fonctionnels automatisés (niveau service ou IHM)\r\n\r\n \r\n\r\nCompétences et qualifications\r\n\r\nPrincipaux langages :\r\n\r\n- Java 1.7 -&gt; 1.8\r\n\r\n- Oracle SQL\r\n\r\nFramework :\r\n\r\n- SPRING 4 (core, security, mvc, jdbc, batch, boot)\r\n\r\n- Angular4 (appli RDM)\r\n\r\n- AngularJS\r\n\r\nArchitecture Web Services :\r\n\r\n- REST / Jersey \r\n\r\nBase de données :\r\n\r\n- Oracle 11g\r\n\r\nUsine Logicielle / outils de test et qualité :\r\n\r\n- Junit (Test unitaire)\r\n\r\n- Cucumber (Test fonctionnel)\r\n\r\n- Plate-forme de dev sur AWS (Cloud Formation)\r\n\r\nCraftmanship\r\n\r\n- Clean code\r\n\r\n- Code Review\r\n\r\nAvantages\r\n\r\n    Salaire attractif \r\n    Possibilités d&rsquo;évolution\r\n    Groupe en forte croissance",
                    'experienceLevel' => 'junior',
                    'minAnnualSalary' => 60000,
                    'maxAnnualSalary' => 60000,
                    'minDailySalary' => null,
                    'maxDailySalary' => null,
                    'currency' => 'USD',
                    'contracts' => [Contract::PERMANENT],
                    'duration' => null,
                    'renewable' => false,
                    'remoteMode' => 'full',
                    'applicationsCount' => 0,
                    'job' => [
                        'id' => 83,
                        'name' => 'Développeur Java',
                        'slug' => 'developpeur-java',
                    ],
                    'location' => [
                        '@type' => 'Location',
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
                    'startsAt' => '2022-03-21T00:00:00+01:00',
                    'company' => [
                        '@id' => '/companies/company-3',
                        '@type' => 'Company',
                        'id' => 3,
                        'name' => 'Company 3',
                        'description' => 'Company 3 // Description',
                        'directoryFreeWork' => false,
                        'logo' => [
                            'small' => 'https://api.freework.localhost/media/cache/resolve/company_logo_small/company-3-logo.jpg',
                            'medium' => 'https://api.freework.localhost/media/cache/resolve/company_logo_medium/company-3-logo.jpg',
                        ],
                        'coverPicture' => null,
                    ],
                    'publishedAt' => (new \DateTime())->modify('-1 day')->setTime(8, 00)->format(\DateTime::RFC3339),
                    'annualSalary' => "60k\u{a0}\$US",
                    'dailySalary' => null,
                    'published' => true,
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
                    ],
                ],
            ],
            'hydra:totalItems' => 5,
        ]);

        $client->request('GET', '/job_postings?minDailySalary=400');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains([
            '@context' => '/contexts/JobPosting',
            '@id' => '/job_postings',
            '@type' => 'hydra:Collection',
            'hydra:member' => [],
            'hydra:totalItems' => 2,
        ]);

        $client->request('GET', '/job_postings?minDailySalary=400&minAnnualSalary=60000');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains([
            '@context' => '/contexts/JobPosting',
            '@id' => '/job_postings',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 7,
            'hydra:member' => [
                [
                    '@id' => '/job_postings/51',
                    '@type' => 'JobPosting',
                    'id' => 51,
                    'title' => 'Développeur Java 8 ans Exp - IDF - (H/F)',
                    'slug' => 'developpeur-java-8-ans-exp-idf-h-f',
                    'companyDescription' => null,
                    'experienceLevel' => 'junior',
                    'minAnnualSalary' => 60000,
                    'maxAnnualSalary' => 60000,
                    'minDailySalary' => null,
                    'maxDailySalary' => null,
                    'currency' => 'USD',
                    'contracts' => [
                        'permanent',
                    ],
                    'duration' => null,
                    'renewable' => false,
                    'remoteMode' => 'full',
                    'applicationType' => 'turnover',
                    'applicationContact' => null,
                    'applicationUrl' => null,
                    'applicationsCount' => 0,
                    'job' => [
                        'id' => 83,
                        'name' => 'Développeur Java',
                        'slug' => 'developpeur-java',
                    ],
                    'location' => [
                        '@type' => 'Location',
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
                    'startsAt' => '2022-03-21T00:00:00+01:00',
                    'reference' => null,
                    'company' => [
                        '@id' => '/companies/company-3',
                        '@type' => 'Company',
                        'id' => 3,
                        'name' => 'Company 3',
                        'slug' => 'company-3',
                        'description' => 'Company 3 // Description',
                        'directoryFreeWork' => false,
                        'logo' => [
                            'small' => 'https://api.freework.localhost/media/cache/resolve/company_logo_small/company-3-logo.jpg',
                            'medium' => 'https://api.freework.localhost/media/cache/resolve/company_logo_medium/company-3-logo.jpg',
                        ],
                        'coverPicture' => null,
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
                    ],
                    'publishedAt' => (new \DateTime())->modify('-1 day')->setTime(8, 00)->format(\DateTime::RFC3339),
                    'published' => true,
                    'annualSalary' => "60k\u{a0}\$US",
                    'dailySalary' => null,
                ],
                [
                    '@id' => '/job_postings/38',
                    '@type' => 'JobPosting',
                    'id' => 38,
                    'title' => 'Consultant en Poste de travail F/H',
                    'slug' => 'consultant-en-poste-de-travail-f-h',
                    'experienceLevel' => 'intermediate',
                    'minAnnualSalary' => 50000,
                    'maxAnnualSalary' => 50000,
                    'minDailySalary' => 450,
                    'maxDailySalary' => 450,
                    'currency' => 'GBP',
                    'contracts' => [
                        'permanent',
                    ],
                    'duration' => 5,
                    'renewable' => false,
                    'remoteMode' => 'partial',
                    'applicationType' => 'turnover',
                    'applicationContact' => null,
                    'applicationUrl' => null,
                    'applicationsCount' => 0,
                    'location' => [
                        '@type' => 'Location',
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
                    'startsAt' => '2021-11-08T00:00:00+01:00',
                    'reference' => null,
                    'company' => [
                        '@id' => '/companies/company-2',
                        '@type' => 'Company',
                        'id' => 2,
                        'name' => 'Company 2',
                        'slug' => 'company-2',
                        'description' => 'Company 2 // Description',
                        'directoryFreeWork' => false,
                        'logo' => [
                            'small' => 'https://api.freework.localhost/media/cache/resolve/company_logo_small/company-2-logo.jpg',
                            'medium' => 'https://api.freework.localhost/media/cache/resolve/company_logo_medium/company-2-logo.jpg',
                        ],
                        'coverPicture' => null,
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
                    'publishedAt' => '2021-04-20T17:25:12+02:00',
                    'published' => true,
                    'annualSalary' => "50k\u{a0}£GB",
                    'dailySalary' => "450\u{a0}£GB",
                ],
            ],
        ]);
    }

    public function testWithPublishedSinceFilter(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/job_postings?publishedSince=less_than_24_hours');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains([
            '@context' => '/contexts/JobPosting',
            '@id' => '/job_postings',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/job_postings/50',
                    '@type' => 'JobPosting',
                    'id' => 50,
                    'title' => 'Ingénieur de Production F/H',
                    'slug' => 'ingenieur-de-production-f-h-1',
                    'description' => "ESN indépendante et innovante, DCS EASYWARE experte dans les métiers de l&rsquo;IT depuis 50 ans, s&rsquo;adapte sans cesse aux exigences d&rsquo;un marché en perpétuelle mutation liée aux évolutions numériques.\r\n\r\nNous vous offrons la possibilité d&rsquo;intégrer une entreprise humaine de près de 800 professionnels des métiers de l&rsquo;infrastructure et de la production informatique. Vous contribuerez au développement ambitieux du Groupe DCS, tourné également vers l&rsquo;International, grâce à des équipes implantées dans les plus grandes capitales Européennes.\r\n\r\nParallèlement, DCS demeure par ses implantations régionales, fidèle à ses valeurs fondatrices de proximité et de management bienveillant.\r\n\r\nVous pourrez vous épanouir et évoluer au fil de missions diversifiées constituées par les services que nous délivrons auprès de nos clients et partenaires dans des secteurs d&rsquo;activité variés (banque-assurance, industrie, luxe, administration-collectivité, retail-grande distribution, commerces et services,&#x2026;).\r\n\r\nSoucieux de la satisfaction et de l&rsquo;épanouissement de chacun, nous privilégions des collaborations de long terme et la recherche d&rsquo;enrichissement réciproque.\n\nNous cherchons aujourd&rsquo;hui à renforcer nos équipes. Vous serez en lien diect avec notre manager Frédérick.\r\n \r\n Allez, on vous en dit plus &#x2026;\r\n \r\n Intégré.e au sein du Service IT Infrastructure &amp; Production, vous interviendrez principalement dans les domaines suivants :\r\n\r\n   La supervision :\r\n\r\n   Participation aux études et choix des outils\r\n   Configuration et définition des alertes\r\n   Production des tableaux de bord\r\n   Suivi, mise à jour et/ou évolution des outils et solutions de supervision\r\n\r\n   La gestion opérationnelle :\r\n\r\n   Etudes, intégration et évolution des outils d&rsquo;ordonnancement\r\n   Identification et intégration des travaux planifiés dans l&rsquo;ordonnanceur\r\n   Suivi du bon déroulement des tâches planifiées et réalisation d&rsquo;actions correctrices\r\n   Production de rapports\r\n\r\n   La mise en production :\r\n\r\n   Participation à la mise en production des nouvelles infrastructures relatives à la supervision et l&rsquo;ordonnancement\r\n   Documentation et mise à jour les procédures d&rsquo;exploitation\n\nEt si on parlait de vous ?\r\n \r\n Diplômé.e d&rsquo;un Bac+3 ou équivalent, vous justifiez d&rsquo;une première expérience sur un poste similaire.\r\n \r\n Vous maîtrisez les outils de supervision et d&rsquo;ordonnancement (Centreon), et connaissez les processus ITIL.\r\n \r\n Vous avez aussi des compétences générales en gestion des infrastructures (Windows Server, Linux, Citrix, VMware) et protocoles (SNMP, WMI, telenet/SSH, API Web Services)\r\n \r\n Force de proposition, vous faites preuve d&rsquo;une grande autonomie et d&rsquo;une réelle capacité d&rsquo;adaptation. Vos qualités relationnelles et rédactionnelles seront autant d&rsquo;atouts qui vont permettront de mener à bien vos missions.\r\n \r\n Notre processus de recrutement : un CDI en 4 étapes ! Tout simplement.\r\n 1. Dans un premier temps, vous échangerez avec l&rsquo;une d&rsquo;entre nous par téléphone : Séverine, Ikram, Clélia ou Marlène\r\n 2. Puis, nous aurons le plaisir de nous rencontrer lors d&rsquo;un entretien physique ou Teams\r\n 3. Pour finir, discutez métier avec l&rsquo;un de nos managers opérationnels\r\n 4. Bienvenue chez nous !",
                    'experienceLevel' => ExperienceLevel::INTERMEDIATE,
                    'minAnnualSalary' => 35000,
                    'maxAnnualSalary' => 55000,
                    'minDailySalary' => null,
                    'maxDailySalary' => null,
                    'currency' => 'EUR',
                    'contracts' => [Contract::PERMANENT],
                    'duration' => null,
                    'renewable' => true,
                    'remoteMode' => RemoteMode::FULL,
                    'applicationsCount' => 0,
                    'job' => [
                        'id' => 116,
                        'name' => 'Ingénieur de production',
                        'slug' => 'ingenieur-de-production',
                    ],
                    'location' => [
                        '@type' => 'Location',
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
                    'startsAt' => '2021-10-10T00:00:00+02:00',
                    'company' => [
                        '@id' => '/companies/company-1',
                        '@type' => 'Company',
                        'id' => 1,
                        'name' => 'Company 1',
                        'directoryFreeWork' => true,
                        'logo' => [
                            'small' => 'https://api.freework.localhost/media/cache/resolve/company_logo_small/company-1-logo.jpg',
                            'medium' => 'https://api.freework.localhost/media/cache/resolve/company_logo_medium/company-1-logo.jpg',
                        ],
                    ],
                    'publishedAt' => (new \DateTime('today'))->setTime(0, 30)->format(\DateTime::RFC3339),
                    'annualSalary' => "35k-55k\u{a0}€",
                    'dailySalary' => null,
                    'published' => true,
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
                ],
            ],
            'hydra:totalItems' => 2,
        ]);
    }

    public function testWithDurationFilter(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/job_postings?minDuration=4');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/JobPosting',
            '@id' => '/job_postings',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/job_postings/38',
                    '@type' => 'JobPosting',
                    'id' => 38,
                    'title' => 'Consultant en Poste de travail F/H',
                    'slug' => 'consultant-en-poste-de-travail-f-h',
                    'description' => "POSTE:\r\n\r\nDans le cadre d&rsquo;un renfort d&rsquo;équipe pour l&rsquo;un de nos clients lyonnais, nous sommes à la recherche d&rsquo;un Consultant en Poste de travail (F/H). Votre mission principale sera de participer activement au MCO (run) de l&rsquo;équipe sur le périmètre poste de travail Windows, MDM/Android. Vous serez donc amener à traiter les incidents et demandes de niveau 2 et plus qui nous seront adressés par le support ou le service desk via l&rsquo;outils de ticket. La quasi-totalité des interventions se font à distance via des outils de prise en main que ce soit sur les tablettes ou les PC Windows.\r\n\r\nVos activités sont les suivantes:\r\n \tTraitement des demandes et incidents via l&rsquo;outil de ticket\r\n \tSupport avancé à distance aux utilisateurs sur leur environnement de travail Windows ou Android\r\n \tRéalisation des actions techniques qui vous seront attribuées\r\n \tAmélioration des processus, des supports de documentation et des bases de connaissance à destination du support.\r\n \tRespect des procédure et de conformité du SI de l&rsquo;entreprise\r\n\r\nPROFIL:\r\n\r\nDe formation supérieure en informatique, vous justifiez d&rsquo;une première expérience réussie sur un poste similaire d&rsquo;au moins trois années.\r\nL&rsquo;écoute et le dialogue font partie de vos atouts afin de bien comprendre les besoins utilisateurs.\r\nLes pannes n&rsquo;affecteront jamais votre bonne humeur. Calme dans toutes les situations, vous avez une bonne appréhension du risque.\r\nAvec l&rsquo;expérience vous avez appris que les problèmes viennent souvent de l&rsquo;interface chaise/clavier.\r\n \r\nCompétences attendues, par ordre d&rsquo;importance:\r\n \tWindows client (Windows 7 / Windows 10 - installation et dépannage)\r\n \tDevices Android et solution de MDM (idéalement Airwatch)\r\n \tOffice 365 (Messagerie / Teams / Sharepoint)\r\n \tActiveDirectory / GPO\r\n \tScripting (Vbscript/Powershell)\r\n \tOutils poste de travail: SCCM/MDT, Antivirus\r\n \tWindows Server\r\n \tITIL\r\n\r\n ",
                    'candidateProfile' => null,
                    'companyDescription' => null,
                    'experienceLevel' => 'intermediate',
                    'minAnnualSalary' => 50000,
                    'maxAnnualSalary' => 50000,
                    'minDailySalary' => 450,
                    'maxDailySalary' => 450,
                    'currency' => 'GBP',
                    'contracts' => [
                        'permanent',
                        'contractor',
                        'apprenticeship',
                    ],
                    'duration' => 5,
                    'durationValue' => 5,
                    'durationPeriod' => 'month',
                    'renewable' => false,
                    'remoteMode' => 'partial',
                    'applicationType' => 'turnover',
                    'applicationContact' => null,
                    'applicationUrl' => null,
                    'applicationEmail' => 'zzidane@free-work.fr',
                    'applicationsCount' => 0,
                    'location' => [
                        '@type' => 'Location',
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
                    'startsAt' => '2021-11-08T00:00:00+01:00',
                    'reference' => null,
                    'company' => [
                        '@id' => '/companies/company-2',
                        '@type' => 'Company',
                        'id' => 2,
                        'name' => 'Company 2',
                        'slug' => 'company-2',
                        'description' => 'Company 2 // Description',
                        'logo' => [
                            'small' => 'https://api.freework.localhost/media/cache/resolve/company_logo_small/company-2-logo.jpg',
                            'medium' => 'https://api.freework.localhost/media/cache/resolve/company_logo_medium/company-2-logo.jpg',
                        ],
                        'directoryFreeWork' => false,
                        'coverPicture' => null,
                    ],
                    'job' => [
                        '@id' => '/jobs/consultant',
                        '@type' => 'Job',
                        'id' => 53,
                        'name' => 'Consultant',
                        'slug' => 'consultant',
                        'nameForContribution' => 'Consultant',
                        'nameForContributionSlug' => 'consultant',
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
                    'publishedAt' => '2021-04-20T17:25:12+02:00',
                    'published' => true,
                    'status' => 'published',
                    'softSkills' => [],
                    'annualSalary' => "50k\u{a0}£GB",
                    'dailySalary' => "450\u{a0}£GB",
                    'expiredAt' => '2021-06-04T17:25:12+02:00',
                ],
                [
                    '@id' => '/job_postings/24',
                    '@type' => 'JobPosting',
                    'id' => 24,
                    'title' => 'Développeur JAVA ',
                    'slug' => 'developpeur-java',
                    'description' => "Tu vas pouvoir te dépasser \r\nEn apportant ton expertise en vue de la modernisation technique et à l&rsquo;évolution fonctionnelle de l&rsquo;application.\r\n\r\nTu aimes \r\n \tFaire évoluer l&rsquo;architecture du SI.\r\n \tGérer des demandes d&rsquo;évolution fonctionnelle et des travaux de maintenance : les coder, les déboguer, les tester.\r\n \tR&D : rechercher des solutions techniques.\r\n \tAssurer la veille technologique, le refactoring et les upgrades.\r\n \tProposer des améliorations en matière de testabilité, de modularité, et de performance.\r\n \tParticiper à la documentation.\r\n \tAssurer du transfert de connaissance.\r\n \tParticiper au processus de recrutement.\r\n \tS&rsquo;intégrer aux rituels d&rsquo;équipe.\r\n \tRevue de code\r\n\r\nTu possèdes \r\n \tJAVA\r\n \tSpring\r\n \tHibernate \r\n \tSQL/Oracle\r\n \tAngular\r\n\r\n\r\nC&rsquo;est tout toi \r\n#ExpérienceMini5Ans #Développement #Banque #Investissement #FortSensDuService #MéthodeAgile #ExcellentRelationnel #FacilitédÉcoute #CapacitédAnalyse #FacilitédAdaptation #BonneCommunication #Autonomie #Autoformation #Flexibilité #OuverturedEsprit #Rigueur #TravailEnEquipe \r\n ",
                    'candidateProfile' => null,
                    'companyDescription' => null,
                    'experienceLevel' => 'junior',
                    'minAnnualSalary' => null,
                    'maxAnnualSalary' => null,
                    'minDailySalary' => null,
                    'maxDailySalary' => null,
                    'currency' => 'USD',
                    'contracts' => [
                        'internship',
                    ],
                    'duration' => 5,
                    'durationValue' => 5,
                    'durationPeriod' => 'month',
                    'renewable' => false,
                    'remoteMode' => 'full',
                    'applicationType' => 'turnover',
                    'applicationContact' => null,
                    'applicationUrl' => null,
                    'applicationEmail' => 'zzidane@free-work.fr',
                    'applicationsCount' => 0,
                    'location' => [
                        '@type' => 'Location',
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
                    'startsAt' => '2020-09-07T00:00:00+02:00',
                    'reference' => null,
                    'company' => [
                        '@id' => '/companies/company-2',
                        '@type' => 'Company',
                        'id' => 2,
                        'name' => 'Company 2',
                        'slug' => 'company-2',
                        'description' => 'Company 2 // Description',
                        'logo' => [
                            'small' => 'https://api.freework.localhost/media/cache/resolve/company_logo_small/company-2-logo.jpg',
                            'medium' => 'https://api.freework.localhost/media/cache/resolve/company_logo_medium/company-2-logo.jpg',
                        ],
                        'directoryFreeWork' => false,
                        'coverPicture' => null,
                    ],
                    'job' => [
                        '@id' => '/jobs/developpeur-java-kotlin-groovy-scala',
                        '@type' => 'Job',
                        'id' => 83,
                        'name' => 'Développeur Java',
                        'slug' => 'developpeur-java',
                        'nameForContribution' => 'Développeur java (kotlin, groovy, scala...)',
                        'nameForContributionSlug' => 'developpeur-java-kotlin-groovy-scala',
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
                    'publishedAt' => '2021-03-24T21:23:52+01:00',
                    'published' => true,
                    'status' => 'published',
                    'softSkills' => [],
                    'annualSalary' => null,
                    'dailySalary' => null,
                    'expiredAt' => '2021-05-08T21:23:52+02:00',
                ],
            ],
            'hydra:totalItems' => 4,
            'hydra:view' => [
                '@id' => '/job_postings?minDuration=4&page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/job_postings?minDuration=4&page=1',
                'hydra:last' => '/job_postings?minDuration=4&page=2',
                'hydra:next' => '/job_postings?minDuration=4&page=2',
            ],
        ]);

        $client->request('GET', '/job_postings?maxDuration=4');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/JobPosting',
            '@id' => '/job_postings',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/job_postings/2',
                    '@type' => 'JobPosting',
                    'id' => 2,
                    'title' => 'Responsable cybersécurité (sans management) (H/F)',
                    'slug' => 'responsable-cybersecurite-sans-management-h-f',
                    'description' => "Fed IT, cabinet entièrement dédié aux recrutements des métiers de l'IT, recherche pour un établissement parisien en finance de marché un ou une Responsable Cybersécurité (sans Management) (H/F) (CDI)\nAu sein de la direction de la sécurité des Systèmes d'information, vous êtes rattaché(e) hiérarchiquement au responsable de cette entité. Votre mission principale sera le suivi et l'analyse de l'efficacité des contrôle permanents et vous intervenez dans le suivi des audits internes et externes. A ce titre ; \n\n-\tVous assurez le suivi des projets et l'aide aux chefs de projet pour l'implémentation des processus de sécurité dès le démarrage des projets\n-\tVous remontez régulièrement au responsable Sécurité les points d'avancements et les blocages éventuels\n-\tVous suivez et aidez pour améliorer, le cas échéant le dispositif de contrôles permanent de niveau 1\n-\tVous Analysez les résultats des évaluations des contrôles et des plans d'actions associés\n-\tVous suivez l'ensemble des processus liés aux missions d'audit en cours\n-\tMettez à jour le tableau de bord de suivi des audits en cours et des recommandations d'audit en cours\n-\tVous consolidez et préparer l'élaboration des reports en collaboration avec les entités concernées\nVous possédez à minima 5 ans d'expérience professionnelle sur un poste similaire. Vous connaissez impérativement le secteur de la finance de marché. \n\nVous avez des connaissances des normes de sécurité (NIST) et êtes certifié(e) ISO 270001. \nVous possédez des connaissances réseaux / Telecoms \nVous êtes obligatoirement bilingue anglais car l'intégralité des documents sont rédigés en anglais. \n\nUne connaissance de la méthodologie Agile est un plus.",
                    'candidateProfile' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent vel commodo dui. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc velit diam, gravida ut auctor et, laoreet eget nunc. Etiam purus risus, auctor ac nisi euismod, condimentum scelerisque ex. Integer aliquet hendrerit velit, non pellentesque nibh interdum quis. Etiam fermentum mi at ex aliquet, ut pretium est posuere. Pellentesque id lectus elit. Duis libero lectus, bibendum non nisi et, ultrices placerat lacus. Nulla vulputate mattis lorem, et aliquet justo vehicula ut. Maecenas varius molestie venenatis.',
                    'companyDescription' => 'Proin et feugiat nisi. Quisque vel augue nibh. Maecenas tortor lacus, tempor cursus pretium sollicitudin, dignissim eu ex. Integer suscipit varius mollis. Quisque tincidunt velit at suscipit feugiat. Pellentesque tempor mi nec ex porttitor commodo. Integer quis imperdiet arcu. Cras quis pretium ligula, in interdum sem. Nam a aliquam ligula. Praesent rutrum neque ac sapien tempus mollis. Nulla sagittis mi sem, quis laoreet augue bibendum a. Donec euismod leo quis tempor facilisis. Interdum et malesuada fames ac ante ipsum primis in faucibus. Nam sit amet augue sapien. Praesent volutpat sapien ante, eu euismod lacus tempus vitae. Sed est lorem, aliquet at malesuada eu, consectetur quis elit.',
                    'experienceLevel' => 'junior',
                    'minAnnualSalary' => 45000,
                    'maxAnnualSalary' => 45000,
                    'minDailySalary' => null,
                    'maxDailySalary' => null,
                    'currency' => 'USD',
                    'contracts' => [
                        'permanent',
                        'apprenticeship',
                    ],
                    'duration' => 3,
                    'durationValue' => 3,
                    'durationPeriod' => 'month',
                    'renewable' => false,
                    'remoteMode' => 'none',
                    'applicationType' => 'turnover',
                    'applicationContact' => null,
                    'applicationUrl' => null,
                    'applicationEmail' => 'zzidane@free-work.fr',
                    'applicationsCount' => 2,
                    'location' => [
                        '@type' => 'Location',
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
                    'reference' => 'ref-0X02',
                    'company' => [
                        '@id' => '/companies/company-1',
                        '@type' => 'Company',
                        'id' => 1,
                        'name' => 'Company 1',
                        'slug' => 'company-1',
                        'description' => 'Company 1 // Description',
                        'logo' => [
                            'small' => 'https://api.freework.localhost/media/cache/resolve/company_logo_small/company-1-logo.jpg',
                            'medium' => 'https://api.freework.localhost/media/cache/resolve/company_logo_medium/company-1-logo.jpg',
                        ],
                        'directoryFreeWork' => true,
                        'coverPicture' => [
                            'medium' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_medium/company-1-picture-1.jpg',
                            'large' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_large/company-1-picture-1.jpg',
                        ],
                    ],
                    'job' => [
                        '@id' => '/jobs/ingenieur-en-cyber-securite',
                        '@type' => 'Job',
                        'id' => 119,
                        'name' => 'Ingénieur en Cybersécurité',
                        'slug' => 'ingenieur-en-cybersecurite',
                        'nameForContribution' => 'Ingénieur en cyber sécurité',
                        'nameForContributionSlug' => 'ingenieur-en-cyber-securite',
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
                    'publishedAt' => '2021-03-19T11:19:01+01:00',
                    'published' => true,
                    'status' => 'published',
                    'softSkills' => [
                        '/soft_skills/4',
                    ],
                    'annualSalary' => "45k\u{a0}\$US",
                    'dailySalary' => null,
                    'expiredAt' => '2021-05-03T11:19:01+02:00',
                ],
                [
                    '@id' => '/job_postings/40',
                    '@type' => 'JobPosting',
                    'id' => 40,
                    'title' => 'Chef de projet expérimenté MOA schémas comptables',
                    'slug' => 'chef-de-projet-experimente-moa-schemas-comptables',
                    'description' => "\r\nDepuis 2003, ACCEO CONSULTING est un Cabinet de niche d&rsquo;une soixantaine de consultants entièrement tournés vers les métiers du risque, de la comptabilité , de la conformité et du reporting réglementaire.\r\n\r\nNous recherchons pour l&rsquo;un de nos clients, une grande entreprise du CAC spécialisée dans le domaine bancaire, un chef de projet expérimenté MOA schémas comptables\r\n\r\nLes principales missions du poste sont:\r\n\r\n  Rédiger des spécifications fonctionnelles à destination des équipes MOA \r\n  Définir la stratégie de recette et le cahier de recette,\r\n  Réaliser les phases de recette et d&rsquo;homologation comptable et assurer la liaison et le support aux utilisateurs,\r\n  Participer aux suivi de production post démarrage en relation avec les équipes Back Office Métiers et les équipes Reporting,\r\nDéfinir les schémas comptables opérationnels qui seront paramétrés dans l&rsquo;interpréteur comptable Finance\r\n  Coordonner les tâches et actions des différents acteurs (MOA et utilisateurs Métiers, équipes informatiques, équipes Reporting) en veillant à la qualité des livrables et au respect des budgets et plannings,\r\n  Identifier l&rsquo;impact des projets sur les principaux reporting finance, risque, liquidité.\r\n\r\n\r\nProfil:\r\n\r\nExpérience minimum de 15 ans en MOA Comptable et schémas comptables (IFRS/FRENCH GAAP)\r\nConnaissance RDJ\r\nConnaissances des reporting risques et liquidité\r\nRédactionnel (anglais), analyses et capacité de synthèse\r\nAutonomie, esprit critique\r\n\r\n\r\nSi cette opportunité vous intéresse et que vous êtes disponible, n&rsquo;hésitez pas à envoyer votre CV à Aïda Iraqui: aida.iraqui@acceo-consulting.com. Sinon n&rsquo;hésitez pas à en parler à votre entourage.      ",
                    'candidateProfile' => null,
                    'companyDescription' => null,
                    'experienceLevel' => 'senior',
                    'minAnnualSalary' => 37000,
                    'maxAnnualSalary' => 37000,
                    'minDailySalary' => null,
                    'maxDailySalary' => null,
                    'currency' => 'USD',
                    'contracts' => [
                        'internship',
                    ],
                    'duration' => 1,
                    'durationValue' => 1,
                    'durationPeriod' => 'month',
                    'renewable' => false,
                    'remoteMode' => 'full',
                    'applicationType' => 'turnover',
                    'applicationContact' => null,
                    'applicationUrl' => null,
                    'applicationEmail' => 'zzidane@free-work.fr',
                    'applicationsCount' => 0,
                    'location' => [
                        '@type' => 'Location',
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
                    'startsAt' => '2021-02-08T00:00:00+01:00',
                    'reference' => null,
                    'company' => [
                        '@id' => '/companies/company-3',
                        '@type' => 'Company',
                        'id' => 3,
                        'name' => 'Company 3',
                        'slug' => 'company-3',
                        'description' => 'Company 3 // Description',
                        'logo' => [
                            'small' => 'https://api.freework.localhost/media/cache/resolve/company_logo_small/company-3-logo.jpg',
                            'medium' => 'https://api.freework.localhost/media/cache/resolve/company_logo_medium/company-3-logo.jpg',
                        ],
                        'directoryFreeWork' => false,
                        'coverPicture' => null,
                    ],
                    'job' => [
                        '@id' => '/jobs/assistant-chef-de-projet',
                        '@type' => 'Job',
                        'id' => 33,
                        'name' => 'Assistant Chef de Projet',
                        'slug' => 'assistant-chef-de-projet',
                        'nameForContribution' => 'Assistant chef de projet',
                        'nameForContributionSlug' => 'assistant-chef-de-projet',
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
                    'publishedAt' => '2021-01-15T06:01:23+01:00',
                    'published' => true,
                    'status' => 'published',
                    'softSkills' => [],
                    'annualSalary' => "37k\u{a0}\$US",
                    'dailySalary' => null,
                    'expiredAt' => '2021-03-01T06:01:23+01:00',
                ],
            ],
            'hydra:totalItems' => 2,
            'hydra:view' => [
                '@id' => '/job_postings?maxDuration=4',
                '@type' => 'hydra:PartialCollectionView',
            ],
        ]);

        $client->request('GET', '/job_postings?minDuration=1&maxDuration=4');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains([
            '@context' => '/contexts/JobPosting',
            '@id' => '/job_postings',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/job_postings/2',
                    '@type' => 'JobPosting',
                    'id' => 2,
                    'title' => 'Responsable cybersécurité (sans management) (H/F)',
                    'slug' => 'responsable-cybersecurite-sans-management-h-f',
                    'description' => "Fed IT, cabinet entièrement dédié aux recrutements des métiers de l'IT, recherche pour un établissement parisien en finance de marché un ou une Responsable Cybersécurité (sans Management) (H/F) (CDI)\nAu sein de la direction de la sécurité des Systèmes d'information, vous êtes rattaché(e) hiérarchiquement au responsable de cette entité. Votre mission principale sera le suivi et l'analyse de l'efficacité des contrôle permanents et vous intervenez dans le suivi des audits internes et externes. A ce titre ; \n\n-\tVous assurez le suivi des projets et l'aide aux chefs de projet pour l'implémentation des processus de sécurité dès le démarrage des projets\n-\tVous remontez régulièrement au responsable Sécurité les points d'avancements et les blocages éventuels\n-\tVous suivez et aidez pour améliorer, le cas échéant le dispositif de contrôles permanent de niveau 1\n-\tVous Analysez les résultats des évaluations des contrôles et des plans d'actions associés\n-\tVous suivez l'ensemble des processus liés aux missions d'audit en cours\n-\tMettez à jour le tableau de bord de suivi des audits en cours et des recommandations d'audit en cours\n-\tVous consolidez et préparer l'élaboration des reports en collaboration avec les entités concernées\nVous possédez à minima 5 ans d'expérience professionnelle sur un poste similaire. Vous connaissez impérativement le secteur de la finance de marché. \n\nVous avez des connaissances des normes de sécurité (NIST) et êtes certifié(e) ISO 270001. \nVous possédez des connaissances réseaux / Telecoms \nVous êtes obligatoirement bilingue anglais car l'intégralité des documents sont rédigés en anglais. \n\nUne connaissance de la méthodologie Agile est un plus.",
                    'experienceLevel' => 'junior',
                    'minAnnualSalary' => 45000,
                    'maxAnnualSalary' => 45000,
                    'minDailySalary' => null,
                    'maxDailySalary' => null,
                    'currency' => 'USD',
                    'contracts' => [
                        'permanent',
                        'apprenticeship',
                    ],
                    'duration' => 3,
                    'durationValue' => 3,
                    'durationPeriod' => 'month',
                    'renewable' => false,
                    'remoteMode' => 'none',
                    'applicationType' => 'turnover',
                    'applicationContact' => null,
                    'applicationUrl' => null,
                    'applicationEmail' => 'zzidane@free-work.fr',
                    'applicationsCount' => 2,
                    'location' => [
                        '@type' => 'Location',
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
                    'reference' => 'ref-0X02',
                    'company' => [
                        '@id' => '/companies/company-1',
                        '@type' => 'Company',
                        'id' => 1,
                        'name' => 'Company 1',
                        'slug' => 'company-1',
                        'description' => 'Company 1 // Description',
                        'logo' => [
                            'small' => 'https://api.freework.localhost/media/cache/resolve/company_logo_small/company-1-logo.jpg',
                            'medium' => 'https://api.freework.localhost/media/cache/resolve/company_logo_medium/company-1-logo.jpg',
                        ],
                        'directoryFreeWork' => true,
                        'coverPicture' => [
                            'medium' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_medium/company-1-picture-1.jpg',
                            'large' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_large/company-1-picture-1.jpg',
                        ],
                    ],
                    'job' => [
                        '@id' => '/jobs/ingenieur-en-cyber-securite',
                        '@type' => 'Job',
                        'id' => 119,
                        'name' => 'Ingénieur en Cybersécurité',
                        'slug' => 'ingenieur-en-cybersecurite',
                        'nameForContribution' => 'Ingénieur en cyber sécurité',
                        'nameForContributionSlug' => 'ingenieur-en-cyber-securite',
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
                    'publishedAt' => '2021-03-19T11:19:01+01:00',
                    'published' => true,
                    'status' => 'published',
                    'softSkills' => [
                        '/soft_skills/4',
                    ],
                    'annualSalary' => "45k\u{a0}\$US",
                    'dailySalary' => null,
                    'expiredAt' => '2021-05-03T11:19:01+02:00',
                ],
                [
                    '@id' => '/job_postings/40',
                    '@type' => 'JobPosting',
                    'id' => 40,
                    'title' => 'Chef de projet expérimenté MOA schémas comptables',
                    'slug' => 'chef-de-projet-experimente-moa-schemas-comptables',
                    'description' => "\r\nDepuis 2003, ACCEO CONSULTING est un Cabinet de niche d&rsquo;une soixantaine de consultants entièrement tournés vers les métiers du risque, de la comptabilité , de la conformité et du reporting réglementaire.\r\n\r\nNous recherchons pour l&rsquo;un de nos clients, une grande entreprise du CAC spécialisée dans le domaine bancaire, un chef de projet expérimenté MOA schémas comptables\r\n\r\nLes principales missions du poste sont:\r\n\r\n  Rédiger des spécifications fonctionnelles à destination des équipes MOA \r\n  Définir la stratégie de recette et le cahier de recette,\r\n  Réaliser les phases de recette et d&rsquo;homologation comptable et assurer la liaison et le support aux utilisateurs,\r\n  Participer aux suivi de production post démarrage en relation avec les équipes Back Office Métiers et les équipes Reporting,\r\nDéfinir les schémas comptables opérationnels qui seront paramétrés dans l&rsquo;interpréteur comptable Finance\r\n  Coordonner les tâches et actions des différents acteurs (MOA et utilisateurs Métiers, équipes informatiques, équipes Reporting) en veillant à la qualité des livrables et au respect des budgets et plannings,\r\n  Identifier l&rsquo;impact des projets sur les principaux reporting finance, risque, liquidité.\r\n\r\n\r\nProfil:\r\n\r\nExpérience minimum de 15 ans en MOA Comptable et schémas comptables (IFRS/FRENCH GAAP)\r\nConnaissance RDJ\r\nConnaissances des reporting risques et liquidité\r\nRédactionnel (anglais), analyses et capacité de synthèse\r\nAutonomie, esprit critique\r\n\r\n\r\nSi cette opportunité vous intéresse et que vous êtes disponible, n&rsquo;hésitez pas à envoyer votre CV à Aïda Iraqui: aida.iraqui@acceo-consulting.com. Sinon n&rsquo;hésitez pas à en parler à votre entourage.      ",
                    'candidateProfile' => null,
                    'companyDescription' => null,
                    'experienceLevel' => 'senior',
                    'minAnnualSalary' => 37000,
                    'maxAnnualSalary' => 37000,
                    'minDailySalary' => null,
                    'maxDailySalary' => null,
                    'currency' => 'USD',
                    'contracts' => [
                        'internship',
                    ],
                    'duration' => 1,
                    'durationValue' => 1,
                    'durationPeriod' => 'month',
                    'renewable' => false,
                    'remoteMode' => 'full',
                    'applicationType' => 'turnover',
                    'applicationContact' => null,
                    'applicationUrl' => null,
                    'applicationEmail' => 'zzidane@free-work.fr',
                    'applicationsCount' => 0,
                    'location' => [
                        '@type' => 'Location',
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
                    'startsAt' => '2021-02-08T00:00:00+01:00',
                    'reference' => null,
                    'company' => [
                        '@id' => '/companies/company-3',
                        '@type' => 'Company',
                        'id' => 3,
                        'name' => 'Company 3',
                        'slug' => 'company-3',
                        'description' => 'Company 3 // Description',
                        'logo' => [
                            'small' => 'https://api.freework.localhost/media/cache/resolve/company_logo_small/company-3-logo.jpg',
                            'medium' => 'https://api.freework.localhost/media/cache/resolve/company_logo_medium/company-3-logo.jpg',
                        ],
                        'directoryFreeWork' => false,
                        'coverPicture' => null,
                    ],
                    'job' => [
                        '@id' => '/jobs/assistant-chef-de-projet',
                        '@type' => 'Job',
                        'id' => 33,
                        'name' => 'Assistant Chef de Projet',
                        'slug' => 'assistant-chef-de-projet',
                        'nameForContribution' => 'Assistant chef de projet',
                        'nameForContributionSlug' => 'assistant-chef-de-projet',
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
                    'publishedAt' => '2021-01-15T06:01:23+01:00',
                    'published' => true,
                    'status' => 'published',
                    'softSkills' => [],
                    'annualSalary' => "37k\u{a0}\$US",
                    'dailySalary' => null,
                    'expiredAt' => '2021-03-01T06:01:23+01:00',
                ],
            ],
            'hydra:totalItems' => 2,
            'hydra:view' => [
                '@id' => '/job_postings?minDuration=1&maxDuration=4',
                '@type' => 'hydra:PartialCollectionView',
            ],
        ]);
    }

    public function testWithLocationFilter(): void
    {
        // Whole country - 200 results
        $client = static::createFreeWorkClient();
        $client->request('GET', '/job_postings?locationKeys=fr~~~');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains([
            'hydra:totalItems' => 51,
        ]);

        // ile-de-france region - 22 results
        $client->request('GET', '/job_postings?locationKeys=fr~ile-de-france~~');
        self::assertJsonContains([
            'hydra:totalItems' => 24,
        ]);

        // paris city - 9 results
        $client->request('GET', '/job_postings?locationKeys=fr~ile-de-france~~paris');
        self::assertJsonContains([
            'hydra:totalItems' => 10,
        ]);

        // auvergne rhone alpes - 6 results
        $client->request('GET', '/job_postings?locationKeys=fr~auvergne-rhone-alpes~~');
        self::assertJsonContains([
            'hydra:totalItems' => 7,
        ]);

        // auvergne rhone alpes + paris city - 15 results
        $client->request('GET', '/job_postings?locationKeys=fr~auvergne-rhone-alpes~~,fr~ile-de-france~~paris');
        self::assertJsonContains([
            'hydra:totalItems' => 17,
        ]);
    }

    public function testWithJobsFilter(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/job_postings?jobs=responsable-gestionnaire-dapplication');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertMatchesJsonSchema([
            '@context' => '/contexts/JobPosting',
            '@id' => '/job_postings',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    'title' => 'Ingénieur Informatique C ++ F/H',
                    'job' => [
                        '@id' => '/jobs/responsable-gestionnaire-dapplication',
                        '@type' => 'Job',
                        'id' => 150,
                        'name' => "Responsable d'Applications Techniques",
                        'slug' => 'responsable-dapplications-techniques',
                        'nameForContribution' => "Responsable / gestionnaire d'application",
                        'nameForContributionSlug' => 'responsable-gestionnaire-dapplication',
                    ],
                ],
                [
                    'title' => 'Développeur React et/ou VueJS  CDI (H/F)',
                    'job' => [
                        '@id' => '/jobs/responsable-gestionnaire-dapplication',
                        '@type' => 'Job',
                        'id' => 150,
                        'name' => "Responsable d'Applications Techniques",
                        'slug' => 'responsable-dapplications-techniques',
                        'nameForContribution' => "Responsable / gestionnaire d'application",
                        'nameForContributionSlug' => 'responsable-gestionnaire-dapplication',
                    ],
                ],
            ],
            'hydra:totalItems' => 4,
            'hydra:view' => [
                '@id' => '/job_postings?jobs=responsable-gestionnaire-dapplication&page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/job_postings?jobs=responsable-gestionnaire-dapplication&page=1',
                'hydra:last' => '/job_postings?jobs=responsable-gestionnaire-dapplication&page=2',
                'hydra:next' => '/job_postings?jobs=responsable-gestionnaire-dapplication&page=2',
            ],
        ]);
    }

    public function testWithPropertiesFilter(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/job_postings?pagination=false&properties[]=slug');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertMatchesJsonSchema([
            '@context' => '/contexts/JobPosting',
            '@id' => '/job_postings',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 50,
            'hydra:page' => 1,
            'hydra:pageCount' => 1,
            'hydra:member' => [
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/50',
                    '@type' => 'JobPosting',
                    'slug' => 'ingenieur-de-production-f-h-1',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/23',
                    '@type' => 'JobPosting',
                    'slug' => 'consultant-talend-confirm',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/18',
                    '@type' => 'JobPosting',
                    'slug' => 'expert-dba-sql-server-oracle',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/43',
                    '@type' => 'JobPosting',
                    'slug' => 'ingenieur-systeme-windows',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/17',
                    '@type' => 'JobPosting',
                    'slug' => 'analyste-exploitation-h-f',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/34',
                    '@type' => 'JobPosting',
                    'slug' => 'concepteur-developpeur-java-h-f',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/7',
                    '@type' => 'JobPosting',
                    'slug' => 'ingenieur-informatique-c-f-h',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/tech-lead-jee-scrum-nantes-f-h',
                    '@type' => 'JobPosting',
                    'slug' => 'tech-lead-jee-scrum-nantes-f-h',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/15',
                    '@type' => 'JobPosting',
                    'slug' => 'developpeur-java-5-ans-exp-idf-h-f',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/38',
                    '@type' => 'JobPosting',
                    'slug' => 'consultant-en-poste-de-travail-f-h',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/16',
                    '@type' => 'JobPosting',
                    'slug' => 'manager-de-transition',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/37',
                    '@type' => 'JobPosting',
                    'slug' => 'comptable-fournisseur-h-f-1',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/41',
                    '@type' => 'JobPosting',
                    'slug' => 'consultant-biztalk',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/20',
                    '@type' => 'JobPosting',
                    'slug' => 'qa',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/28',
                    '@type' => 'JobPosting',
                    'slug' => 'expert-talend-esb',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/10',
                    '@type' => 'JobPosting',
                    'slug' => 'developpeur-web-php-confirme-f-h',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/24',
                    '@type' => 'JobPosting',
                    'slug' => 'developpeur-java',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/2',
                    '@type' => 'JobPosting',
                    'slug' => 'responsable-cybersecurite-sans-management-h-f',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/35',
                    '@type' => 'JobPosting',
                    'slug' => 'incident-manager',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/48',
                    '@type' => 'JobPosting',
                    'slug' => 'technicien-support-confirme-h-f',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/49',
                    '@type' => 'JobPosting',
                    'slug' => 'administrateur-landesk-ivanti-h-f',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/21',
                    '@type' => 'JobPosting',
                    'slug' => 'frontend-developer',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/45',
                    '@type' => 'JobPosting',
                    'slug' => 'consultor-tecnico-crm-dynamics-barcelona-remote-role',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/33',
                    '@type' => 'JobPosting',
                    'slug' => 'testeur-logiciel-anglais-courant-f-h',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/26',
                    '@type' => 'JobPosting',
                    'slug' => 'team-leader',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/32',
                    '@type' => 'JobPosting',
                    'slug' => 'amoa-technique-java-f-h',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/6',
                    '@type' => 'JobPosting',
                    'slug' => 'developpeur-se-php-symfony-cdi-h-f',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/39',
                    '@type' => 'JobPosting',
                    'slug' => 'ingenieur-en-strategie-de-test-recette-fonctionnel-a-paris-h-f',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/46',
                    '@type' => 'JobPosting',
                    'slug' => 'head-of-engineering-h-f-stratup-robotique',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/13',
                    '@type' => 'JobPosting',
                    'slug' => 'administrateur-support-reseau-n2-f-h',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/19',
                    '@type' => 'JobPosting',
                    'slug' => 'dba-h-f',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/47',
                    '@type' => 'JobPosting',
                    'slug' => 'analyste-d-rsquo-exploitation-et-support-systeme-reseaux-h-f',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/4',
                    '@type' => 'JobPosting',
                    'slug' => 'ingenieur-systeme-et-reseaux-f-h',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/25',
                    '@type' => 'JobPosting',
                    'slug' => 'comptable-fournisseur-h-f',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/5',
                    '@type' => 'JobPosting',
                    'slug' => 'developpeur-react-et-ou-vuejs-cdi-h-f',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/3',
                    '@type' => 'JobPosting',
                    'slug' => 'ingenieur-bi-f-h',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/27',
                    '@type' => 'JobPosting',
                    'slug' => 'developpeur-net',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/29',
                    '@type' => 'JobPosting',
                    'slug' => 'developpeur-ssis-h-f',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/40',
                    '@type' => 'JobPosting',
                    'slug' => 'chef-de-projet-experimente-moa-schemas-comptables',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/12',
                    '@type' => 'JobPosting',
                    'slug' => 'analyste-support-technicien-ne-support-confirme-f-h',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/44',
                    '@type' => 'JobPosting',
                    'slug' => 'tech-lead-java',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/14',
                    '@type' => 'JobPosting',
                    'slug' => 'developpeur-bi',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/36',
                    '@type' => 'JobPosting',
                    'slug' => 'consultant-sap-fi-rh',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/8',
                    '@type' => 'JobPosting',
                    'slug' => 'responsable-informatique-f-h',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/30',
                    '@type' => 'JobPosting',
                    'slug' => 'pilote-technique-soc-analyste-n2-soc-avignon',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/11',
                    '@type' => 'JobPosting',
                    'slug' => 'ingenieur-d-rsquo-exploitation-h-f',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/31',
                    '@type' => 'JobPosting',
                    'slug' => 'sysops-f-h',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/1',
                    '@type' => 'JobPosting',
                    'slug' => 'responsable-applicatifs-finance-h-f-cdi',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/9',
                    '@type' => 'JobPosting',
                    'slug' => 'developpeur-c-net-confirme-f-h',
                ],
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings/22',
                    '@type' => 'JobPosting',
                    'slug' => 'ingenieur-de-production-f-h',
                ],
            ],
        ]);
    }

    public function testWithItemsPerPage(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/job_postings?itemsPerPage=30');
        self::assertJsonContains([
            'hydra:view' => [
                'hydra:first' => '/job_postings?itemsPerPage=30&page=1',
                'hydra:last' => '/job_postings?itemsPerPage=30&page=2',
                'hydra:next' => '/job_postings?itemsPerPage=30&page=2',
            ],
        ]);

        $client->request('GET', '/job_postings?itemsPerPage=5');
        self::assertJsonContains([
            'hydra:view' => [
                'hydra:first' => '/job_postings?itemsPerPage=5&page=1',
                'hydra:last' => '/job_postings?itemsPerPage=5&page=11',
                'hydra:next' => '/job_postings?itemsPerPage=5&page=2',
            ],
        ]);

        $client->request('GET', '/job_postings?itemsPerPage=1');
        self::assertJsonContains([
            'hydra:view' => [
                'hydra:first' => '/job_postings?itemsPerPage=1&page=1',
                'hydra:last' => '/job_postings?itemsPerPage=1&page=51',
                'hydra:next' => '/job_postings?itemsPerPage=1&page=2',
            ],
        ]);
    }

    public function testWithSearchWithoutData(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/job_postings?searchKeywords=zxy');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertMatchesJsonSchema([
            '@context' => '/contexts/JobPosting',
            '@id' => '/job_postings',
            '@type' => 'hydra:Collection',
            'hydra:member' => [],
            'hydra:totalItems' => 0,
        ]);
    }

    public static function provideOrderFilterCases(): iterable
    {
        return [
            [
                '/job_postings?searchKeywords=chef%20de%20projet&order=relevance',
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings',
                    '@type' => 'hydra:Collection',
                    'hydra:member' => [
                        [
                            '@id' => '/job_postings/40',
                            '@type' => 'JobPosting',
                            'id' => 40,
                        ],
                        [
                            '@id' => '/job_postings/7',
                            '@type' => 'JobPosting',
                            'id' => 7,
                        ],
                    ],
                    'hydra:totalItems' => 3,
                ],
            ],
            [
                '/job_postings?searchKeywords=chef%20de%20projet&order=date',
                [
                    '@context' => '/contexts/JobPosting',
                    '@id' => '/job_postings',
                    '@type' => 'hydra:Collection',
                    'hydra:member' => [
                        [
                            '@id' => '/job_postings/7',
                            '@type' => 'JobPosting',
                            'id' => 7,
                        ],
                        [
                            '@id' => '/job_postings/40',
                            '@type' => 'JobPosting',
                            'id' => 40,
                        ],
                    ],
                    'hydra:totalItems' => 3,
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideOrderFilterCases
     */
    public function testOrderFilter(string $query, array $expected): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', $query);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains($expected);
    }
}
