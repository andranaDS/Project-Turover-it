<?php

namespace App\JobPosting\Tests\Functional\Turnover\JobPosting;

use App\Tests\Functional\ApiTestCase;
use Carbon\Carbon;

class CompaniesMineJobPostingsTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/companies/mine/job_postings');
        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedOnSecondaryRecruiter(): void
    {
        $client = static::createTurnoverAuthenticatedClient('jesse.pinkman@breaking-bad.com');
        $client->request('GET', '/companies/mine/job_postings');
        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedOnPrimaryRecruiter1(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/companies/mine/job_postings');

        self::assertResponseStatusCodeSame(200);
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
                    'candidateProfile' => null,
                    'companyDescription' => null,
                    'experienceLevel' => 'intermediate',
                    'minAnnualSalary' => 35000,
                    'maxAnnualSalary' => 55000,
                    'minDailySalary' => null,
                    'maxDailySalary' => null,
                    'currency' => 'EUR',
                    'contracts' => [
                        'permanent',
                    ],
                    'duration' => null,
                    'durationValue' => null,
                    'durationPeriod' => null,
                    'renewable' => true,
                    'remoteMode' => 'full',
                    'applicationType' => 'turnover',
                    'applicationContact' => null,
                    'applicationUrl' => null,
                    'applicationEmail' => 'zzidane@free-work.fr',
                    'applicationsCount' => 0,
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
                    'startsAt' => '2021-10-10T00:00:00+02:00',
                    'reference' => null,
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
                        '@id' => '/jobs/ingenieur-de-production',
                        '@type' => 'Job',
                        'id' => 116,
                        'name' => 'Ingénieur de production',
                        'slug' => 'ingenieur-de-production',
                        'nameForContribution' => 'Ingénieur de production',
                        'nameForContributionSlug' => 'ingenieur-de-production',
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
                    'viewsCount' => 9,
                    'createdAt' => Carbon::today()->setTime(0, 30)->format(\DateTimeInterface::RFC3339),
                    'updatedAt' => Carbon::today()->setTime(0, 30)->format(\DateTimeInterface::RFC3339),
                    'publishedAt' => Carbon::today()->setTime(0, 30)->format(\DateTimeInterface::RFC3339),
                    'expiredAt' => Carbon::today()->addDays(45)->setTime(0, 30)->format(\DateTimeInterface::RFC3339),
                    'published' => true,
                    'status' => 'published',
                    'softSkills' => [],
                    'annualSalary' => "35k-55k\u{a0}€",
                    'dailySalary' => null,
                ],
                [
                    '@id' => '/job_postings/18',
                    '@type' => 'JobPosting',
                    'id' => 18,
                    'title' => 'Expert DBA SQL Server / Oracle ',
                    'slug' => 'expert-dba-sql-server-oracle',
                    'description' => "Le bénéficiaire recherche une prestation d&rsquo;expertise en administration des Databases des technologies SQL Server et Oracle \r\nPrestations demandées :             \r\n\r\nLes missions sont :\r\n- Accompagnement projet métier dès la phase de cadrage pour des technologies database SQL Server / Oracle \r\n- Maintien en condition opérationnelle (sauvegarde, audit, AAG) des databases SQL Server / Oracle \r\n- Expertise pour escalade des traitement d&rsquo;incidents sur databases SQL Server / Oracle \r\n- Audit/tunning Database SQL Server / Oracle \r\n- Construction ToolChain SQL Server / Oracle \r\n- Mise en production de packages applicatifs pour le compte de nos métier sur technologie SQL Server en HO comme HNO\r\n- Mise en place MCO automatisé de suivi des sauvegardes applicatives\r\n- Réalisation d&rsquo;astreinte si nécessaire sur applications métiers hébergeant des databses SQL Server / Oracle \r\n\r\n\r\n\r\n  ",
                    'candidateProfile' => null,
                    'companyDescription' => null,
                    'experienceLevel' => 'senior',
                    'minAnnualSalary' => 45000,
                    'maxAnnualSalary' => 45000,
                    'minDailySalary' => null,
                    'maxDailySalary' => null,
                    'currency' => 'GBP',
                    'contracts' => [
                        'permanent',
                    ],
                    'duration' => null,
                    'durationValue' => null,
                    'durationPeriod' => null,
                    'renewable' => true,
                    'remoteMode' => 'partial',
                    'applicationType' => 'turnover',
                    'applicationContact' => null,
                    'applicationUrl' => null,
                    'applicationEmail' => 'zzidane@free-work.fr',
                    'applicationsCount' => 0,
                    'location' => [
                        '@type' => 'Location',
                        'street' => null,
                        'locality' => 'Cormeilles-en-Parisis',
                        'postalCode' => '95240',
                        'adminLevel1' => 'Île-de-France',
                        'adminLevel2' => "Val-d'Oise",
                        'country' => 'France',
                        'countryCode' => 'FR',
                        'latitude' => '48.9759637',
                        'longitude' => '2.1998877',
                        'key' => 'fr~ile-de-france~val-d-oise~cormeilles-en-parisis',
                        'label' => 'Cormeilles-en-Parisis, Île-de-France',
                        'shortLabel' => 'Cormeilles-en-Parisis (95)',
                    ],
                    'startsAt' => '2021-03-02T00:00:00+01:00',
                    'reference' => null,
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
                        '@id' => '/jobs/developpeur-base-de-donnees-sql-pl-sql-oracle',
                        '@type' => 'Job',
                        'id' => 84,
                        'name' => 'Développeur Oracle',
                        'slug' => 'developpeur-oracle',
                        'nameForContribution' => 'Développeur base de données (SQL, PL/SQL, oracle...)',
                        'nameForContributionSlug' => 'developpeur-base-de-donnees-sql-pl-sql-oracle',
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
                    'viewsCount' => 9,
                    'createdAt' => '2021-05-27T19:06:50+02:00',
                    'updatedAt' => '2021-05-27T19:06:50+02:00',
                    'publishedAt' => '2021-05-27T19:06:50+02:00',
                    'published' => true,
                    'status' => 'published',
                    'softSkills' => [],
                    'annualSalary' => "45k\u{a0}£GB",
                    'dailySalary' => null,
                    'expiredAt' => '2021-07-11T19:06:50+02:00',
                ],
            ],
            'hydra:totalItems' => 10,
            'hydra:view' => [
                '@id' => '/companies/mine/job_postings?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/companies/mine/job_postings?page=1',
                'hydra:last' => '/companies/mine/job_postings?page=5',
                'hydra:next' => '/companies/mine/job_postings?page=2',
            ],
        ]);
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedOnPrimaryRecruiter2(): void
    {
        $client = static::createTurnoverAuthenticatedClient('eddard.stark@got.com');
        $client->request('GET', '/companies/mine/job_postings');

        self::assertResponseStatusCodeSame(200);
        self::assertJsonContains([
            '@context' => '/contexts/JobPosting',
            '@id' => '/job_postings',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/job_postings/53',
                    '@type' => 'JobPosting',
                    'id' => 53,
                    'title' => 'Développeur NuxtJS (H/F)',
                    'slug' => 'developpeur-nuxtjs-h-f',
                    'description' => "Descriptif du poste : \r\n\r\nEn tant que développeur au sein d&rsquo;une équipe d&rsquo;une quinzaine personnes, vous serez responsable :\r\n\r\n   du développement\r\n   du packaging\r\n   des tests unitaires\r\n   des tests fonctionnels automatisés (niveau service ou IHM)\r\n\r\n \r\n\r\nCompétences et qualifications\r\n\r\nPrincipaux langages :\r\n\r\n- Java 1.7 -&gt; 1.8\r\n\r\n- Oracle SQL\r\n\r\nFramework :\r\n\r\n- SPRING 4 (core, security, mvc, jdbc, batch, boot)\r\n\r\n- Angular4 (appli RDM)\r\n\r\n- AngularJS\r\n\r\nArchitecture Web Services :\r\n\r\n- REST / Jersey \r\n\r\nBase de données :\r\n\r\n- Oracle 11g\r\n\r\nUsine Logicielle / outils de test et qualité :\r\n\r\n- Junit (Test unitaire)\r\n\r\n- Cucumber (Test fonctionnel)\r\n\r\n- Plate-forme de dev sur AWS (Cloud Formation)\r\n\r\nCraftmanship\r\n\r\n- Clean code\r\n\r\n- Code Review\r\n\r\nAvantages\r\n\r\n    Salaire attractif \r\n    Possibilités d&rsquo;évolution",
                    'candidateProfile' => null,
                    'companyDescription' => null,
                    'experienceLevel' => 'senior',
                    'minAnnualSalary' => null,
                    'maxAnnualSalary' => null,
                    'minDailySalary' => 500,
                    'maxDailySalary' => 550,
                    'currency' => 'EUR',
                    'contracts' => [
                        'intercontract',
                    ],
                    'duration' => 10,
                    'durationValue' => 10,
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
                        'street' => null,
                        'locality' => 'Bordeaux',
                        'postalCode' => null,
                        'adminLevel1' => 'Nouvelle-Aquitaine',
                        'adminLevel2' => 'Gironde',
                        'country' => 'France',
                        'countryCode' => 'FR',
                        'latitude' => '44.8412250',
                        'longitude' => '-0.5800364',
                        'key' => 'fr~nouvelle-aquitaine~gironde~bordeaux',
                        'label' => 'Bordeaux, Nouvelle-Aquitaine',
                        'shortLabel' => 'Bordeaux',
                    ],
                    'startsAt' => '2022-09-21T00:00:00+02:00',
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
                        '@id' => '/jobs/api-developpeur',
                        '@type' => 'Job',
                        'id' => 22,
                        'name' => 'API Développeur',
                        'slug' => 'api-developpeur',
                        'nameForContribution' => 'API Développeur',
                        'nameForContributionSlug' => 'api-developpeur',
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
                    'viewsCount' => 9,
                    'createdAt' => '2022-09-17T21:34:41+02:00',
                    'publishedAt' => '2022-09-17T21:34:41+02:00',
                    'published' => true,
                    'status' => 'published',
                    'softSkills' => [
                    ],
                    'annualSalary' => null,
                    'dailySalary' => "500-550\u{a0}€",
                    'expiredAt' => '2022-11-01T21:34:41+01:00',
                ],
                [
                    '@id' => '/job_postings/54',
                    '@type' => 'JobPosting',
                    'id' => 54,
                    'title' => 'Développeur Zend 10 ans expériences  (H/F)',
                    'slug' => 'developpeur-zend-10-ans-experiences-h-f',
                    'description' => "Descriptif du poste : \r\n\r\nEn tant que développeur au sein d&rsquo;une équipe d&rsquo;une quinzaine personnes, vous serez responsable :\r\n\r\n   du développement\r\n   du packaging\r\n   des tests unitaires\r\n   des tests fonctionnels automatisés (niveau service ou IHM)\r\n\r\n \r\n\r\nCompétences et qualifications\r\n\r\nPrincipaux langages :\r\n\r\n- Java 1.7 -&gt; 1.8\r\n\r\n- Oracle SQL\r\n\r\nFramework :\r\n\r\n- SPRING 4 (core, security, mvc, jdbc, batch, boot)\r\n\r\n- Angular4 (appli RDM)\r\n\r\n- AngularJS\r\n\r\nArchitecture Web Services :\r\n\r\n- REST / Jersey \r\n\r\nBase de données :\r\n\r\n- Oracle 11g\r\n\r\nUsine Logicielle / outils de test et qualité :\r\n\r\n- Junit (Test unitaire)\r\n\r\n- Cucumber (Test fonctionnel)\r\n\r\n- Plate-forme de dev sur AWS (Cloud Formation)\r\n\r\nCraftmanship\r\n\r\n- Clean code\r\n\r\n- Code Review\r\n\r\nAvantages\r\n\r\n    Salaire attractif \r\n    Possibilités d&rsquo;évolution",
                    'candidateProfile' => null,
                    'companyDescription' => null,
                    'experienceLevel' => 'senior',
                    'minAnnualSalary' => null,
                    'maxAnnualSalary' => null,
                    'minDailySalary' => 850,
                    'maxDailySalary' => 1000,
                    'currency' => 'EUR',
                    'contracts' => [
                        'intercontract',
                    ],
                    'duration' => 3,
                    'durationValue' => 3,
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
                    'startsAt' => '2022-09-21T00:00:00+02:00',
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
                        '@id' => '/jobs/api-developpeur',
                        '@type' => 'Job',
                        'id' => 22,
                        'name' => 'API Développeur',
                        'slug' => 'api-developpeur',
                        'nameForContribution' => 'API Développeur',
                        'nameForContributionSlug' => 'api-developpeur',
                    ],
                    'skills' => [
                        [
                            '@id' => '/skills/1',
                            '@type' => 'Skill',
                            'id' => 1,
                            'name' => 'php',
                            'slug' => 'php',
                        ],
                    ],
                    'viewsCount' => 9,
                    'createdAt' => '2022-09-17T21:34:41+02:00',
                    'publishedAt' => '2022-09-17T21:34:41+02:00',
                    'published' => true,
                    'status' => 'published',
                    'softSkills' => [
                    ],
                    'annualSalary' => null,
                    'dailySalary' => "850-1k\u{a0}€",
                    'expiredAt' => '2022-11-01T21:34:41+01:00',
                ],
            ],
            'hydra:totalItems' => 12,
            'hydra:view' => [
                '@id' => '/companies/mine/job_postings?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/companies/mine/job_postings?page=1',
                'hydra:last' => '/companies/mine/job_postings?page=6',
                'hydra:next' => '/companies/mine/job_postings?page=2',
            ],
        ]);
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testStatusFilter(): void
    {
        $client = static::createTurnoverAuthenticatedClient('eddard.stark@got.com');
        $client->request('GET', '/companies/mine/job_postings?status=draft');

        self::assertResponseStatusCodeSame(200);
        self::assertJsonContains([
            '@context' => '/contexts/JobPosting',
            '@id' => '/job_postings',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/job_postings/55',
                    '@type' => 'JobPosting',
                    'id' => 55,
                    'title' => 'Développeur fullstack Symfony-Vue.js',
                    'slug' => 'developpeur-fullstack-symfony-vue-js',
                    'description' => "#fintech : Je recrute en #CDI un Développeur fullstack Symfony/Vue.js sénior pour un client final FinTech-RegTech à Saint-Cloud (92).\r\n\r\n Vous avez obligatoirement minimum 5 ans d’expérience en tant que développeur PHP/Symfony. Vous avez également de bonnes notions en développement front end. \r\n\r\n Le client est un éditeur français ayant deux activités : une division Fintech ainsi qu’une division Conversion de Protocoles.\r\n\r\n Les deux activités sont en forte progression et couvrent aussi bien l’édition que l’intégration au sein du SI de ses Clients.\r\n\r\n Classé parmi les Best Place to Work 2022, par l’enquête Great Place to Work pour les sociétés de 50 à 250 salariés ! \r\n\r\n ?: PHP 7-8 / Symfony 4.4 ; Vue.js 3 ; Javascript ; PhpStorm ; AMQP, Stack ELK, Redis, Rest API, SOAP, SGBD, JSON, XML, XSLT \r\n\r\n ?Evolution : professionnalisation rapide en interne .\r\n\r\n Rémunération 55.000Eur - 65.000Eur\r\n\r\n ?Remote partiel et autres avantages.",
                    'candidateProfile' => null,
                    'companyDescription' => null,
                    'experienceLevel' => 'senior',
                    'minAnnualSalary' => null,
                    'maxAnnualSalary' => null,
                    'minDailySalary' => 850,
                    'maxDailySalary' => 1000,
                    'currency' => 'EUR',
                    'contracts' => [
                        'intercontract',
                    ],
                    'duration' => 3,
                    'durationValue' => 3,
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
                    'startsAt' => '2022-09-24T00:00:00+02:00',
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
                        '@id' => '/jobs/developpeur-php-symfony-laravel-drupal',
                        '@type' => 'Job',
                        'id' => 86,
                        'name' => 'Développeur PHP',
                        'slug' => 'developpeur-php',
                        'nameForContribution' => 'Développeur PHP (symfony, laravel, drupal ...)',
                        'nameForContributionSlug' => 'developpeur-php-symfony-laravel-drupal',
                    ],
                    'skills' => [
                        [
                            '@id' => '/skills/1',
                            '@type' => 'Skill',
                            'id' => 1,
                            'name' => 'php',
                            'slug' => 'php',
                        ],
                    ],
                    'viewsCount' => 9,
                    'createdAt' => '2022-08-28T15:00:00+02:00',
                    'publishedAt' => '2022-08-28T15:00:00+02:00',
                    'published' => true,
                    'status' => 'draft',
                    'softSkills' => [
                    ],
                    'annualSalary' => null,
                    'dailySalary' => "850-1k\u{a0}€",
                    'expiredAt' => '2022-10-12T15:00:00+02:00',
                ],
            ],
            'hydra:totalItems' => 1,
        ]);
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}
