<?php

namespace App\JobPosting\Tests\Functional\Turnover\JobPosting;

use App\Tests\Functional\ApiTestCase;
use Carbon\Carbon;

class RecruitersMeJobPostingsTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/recruiters/me/job_postings');
        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedOnPrimaryRecruiter(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/recruiters/me/job_postings');

        self::assertResponseIsSuccessful();
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
                    'softSkills' => [
                    ],
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
                        'adminLevel2' => 'Val-d\'Oise',
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
                    'publishedAt' => '2021-05-27T19:06:50+02:00',
                    'published' => true,
                    'status' => 'published',
                    'softSkills' => [
                    ],
                    'annualSalary' => "45k\u{a0}£GB",
                    'dailySalary' => null,
                    'expiredAt' => '2021-07-11T19:06:50+02:00',
                ],
            ],
            'hydra:totalItems' => 9,
            'hydra:view' => [
                '@id' => '/recruiters/me/job_postings?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/recruiters/me/job_postings?page=1',
                'hydra:last' => '/recruiters/me/job_postings?page=5',
                'hydra:next' => '/recruiters/me/job_postings?page=2',
            ],
        ]);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedOnSecondaryPrimaryRecruiter(): void
    {
        $client = static::createTurnoverAuthenticatedClient('jesse.pinkman@breaking-bad.com');
        $client->request('GET', '/recruiters/me/job_postings');

        self::assertResponseIsSuccessful();
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
                    'viewsCount' => 9,
                    'createdAt' => '2021-03-19T11:19:01+01:00',
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
            ],
            'hydra:totalItems' => 1,
        ]);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testSearchTitleAndReferenceRecruiter(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/recruiters/me/job_postings?q=production');

        self::assertResponseIsSuccessful();
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
                    'softSkills' => [
                    ],
                    'annualSalary' => "35k-55k\u{a0}€",
                    'dailySalary' => null,
                ],
            ],
            'hydra:totalItems' => 1,
        ]);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $client->request('GET', '/recruiters/me/job_postings?q=ref');

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/contexts/JobPosting',
            '@id' => '/job_postings',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/job_postings/1',
                    '@type' => 'JobPosting',
                    'id' => 1,
                    'title' => 'Responsable applicatifs Finance (H/F) (CDI)',
                    'slug' => 'responsable-applicatifs-finance-h-f-cdi',
                    'description' => "Fed IT, cabinet entièrement dédié aux recrutements des métiers de l'IT, recherche pour un établissement parisien en finance de marché un ou une Responsable Support applicatifs Finance (H/F) (CDI)\nAu sein de la direction des Systèmes d'information, votre mission principale sera de maintenir efficacement le fonctionnement des applications métier et de coordonner les équipes support. A ce titre ; \n\n-\tVous assurez le support applicatif auprès des utilisateurs (N1 / N2 / N3). \n-\tVous réalisez les opérations de maintenance ou d'évolution en fonction des contraintes imposées par les marchés financiers. \n-\tVous êtes l'interlocuteur (rice) central(e) entre les chefs de projet, les équipes IT, les équipes de test et les utilisateurs. \n-\tVous participez aux astreintes selon les rotations prévues pour l'équipe. \n-\tVous assurez la gestion et la coordination de l'équipe support IT\nVous possédez à minima 5 ans d'expérience professionnelle sur un poste de support applicatif en environnement financier. \n\nVous avez obligatoirement des connaissances UNIX et SQL. \nVous avez un anglais courant / bilingue car l'intégralité des documents sont rédigés en anglais, certains utilisateurs sont uniquement anglophones. \n\nPoste basé à Paris (75002). \nRémunération : 50-70K fixe en fonction du profil et de l'expérience.",
                    'candidateProfile' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent vel commodo dui. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc velit diam, gravida ut auctor et, laoreet eget nunc. Etiam purus risus, auctor ac nisi euismod, condimentum scelerisque ex. Integer aliquet hendrerit velit, non pellentesque nibh interdum quis. Etiam fermentum mi at ex aliquet, ut pretium est posuere. Pellentesque id lectus elit. Duis libero lectus, bibendum non nisi et, ultrices placerat lacus. Nulla vulputate mattis lorem, et aliquet justo vehicula ut. Maecenas varius molestie venenatis.',
                    'companyDescription' => 'Proin et feugiat nisi. Quisque vel augue nibh. Maecenas tortor lacus, tempor cursus pretium sollicitudin, dignissim eu ex. Integer suscipit varius mollis. Quisque tincidunt velit at suscipit feugiat. Pellentesque tempor mi nec ex porttitor commodo. Integer quis imperdiet arcu. Cras quis pretium ligula, in interdum sem. Nam a aliquam ligula. Praesent rutrum neque ac sapien tempus mollis. Nulla sagittis mi sem, quis laoreet augue bibendum a. Donec euismod leo quis tempor facilisis. Interdum et malesuada fames ac ante ipsum primis in faucibus. Nam sit amet augue sapien. Praesent volutpat sapien ante, eu euismod lacus tempus vitae. Sed est lorem, aliquet at malesuada eu, consectetur quis elit.',
                    'experienceLevel' => 'junior',
                    'minAnnualSalary' => 40000,
                    'maxAnnualSalary' => 40000,
                    'minDailySalary' => null,
                    'maxDailySalary' => null,
                    'currency' => 'GBP',
                    'contracts' => ['permanent'],
                    'duration' => 24,
                    'renewable' => false,
                    'remoteMode' => 'none',
                    'applicationType' => 'turnover',
                    'applicationContact' => null,
                    'applicationUrl' => null,
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
                    'startsAt' => null,
                    'reference' => 'ref-0X01',
                    'company' => [
                        '@id' => '/companies/company-1',
                        '@type' => 'Company',
                        'id' => 1,
                        'name' => 'Company 1',
                        'description' => 'Company 1 // Description',
                        'logo' => [
                            'small' => 'https://api.freework.localhost/media/cache/resolve/company_logo_small/company-1-logo.jpg',
                            'medium' => 'https://api.freework.localhost/media/cache/resolve/company_logo_medium/company-1-logo.jpg',
                        ],
                        'coverPicture' => [
                            'medium' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_medium/company-1-picture-1.jpg',
                            'large' => 'https://api.freework.localhost/media/cache/resolve/company_picture_image_large/company-1-picture-1.jpg',
                        ],
                    ],
                    'publishedAt' => '2020-12-07T02:07:40+01:00',
                    'annualSalary' => "40k\u{a0}£GB",
                    'dailySalary' => null,
                    'published' => true,
                    'job' => [
                        'id' => 150,
                        'name' => "Responsable d'Applications Techniques",
                        'slug' => 'responsable-dapplications-techniques',
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
                ],
            ],
            'hydra:totalItems' => 1,
        ]);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}
