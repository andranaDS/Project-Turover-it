<?php

namespace App\JobPosting\Tests\Functional\FreeWork\JobPosting;

use App\JobPosting\Enum\Contract;
use App\JobPosting\Enum\ExperienceLevel;
use App\JobPosting\Enum\RemoteMode;
use App\Tests\Functional\ApiTestCase;

class JobPostingsGetByCompanyTest extends ApiTestCase
{
    public function testWithExistingCompany(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/companies/company-1/job_postings');

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
            'hydra:totalItems' => 10,
            'hydra:view' => [
                '@id' => '/companies/company-1/job_postings?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/companies/company-1/job_postings?page=1',
                'hydra:last' => '/companies/company-1/job_postings?page=5',
                'hydra:next' => '/companies/company-1/job_postings?page=2',
            ],
        ]);
    }

    public function testWithNonExistentCompany(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/companies/company-non-existent/job_postings');

        self::assertResponseStatusCodeSame(404);
    }

    public function testWithContractsFilter(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/companies/company-2/job_postings?contracts=contractor');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains(['hydra:totalItems' => 2]);

        $client->request('GET', '/companies/company-2/job_postings?contracts=permanent');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains(['hydra:totalItems' => 7]);
    }
}
