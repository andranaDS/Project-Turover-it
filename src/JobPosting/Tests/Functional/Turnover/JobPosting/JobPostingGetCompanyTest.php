<?php

namespace App\JobPosting\Tests\Functional\Turnover\JobPosting;

use App\Tests\Functional\ApiTestCase;

class JobPostingGetCompanyTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/companies/company-1/job_postings');

        self::assertResponseStatusCodeSame(401);
    }

    public static function provideLoggedOnMeCases(): iterable
    {
        yield [
            'expected' => [
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
                        'contracts' => ['permanent', 'intercontract'],
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
            ],
        ];
    }

    /**
     * @dataProvider provideLoggedOnMeCases
     */
    public function testLoggedOnMe(array $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/companies/company-3/job_postings');

        self::assertResponseStatusCodeSame(200);
        self::assertJsonContains($expected);
    }
}
