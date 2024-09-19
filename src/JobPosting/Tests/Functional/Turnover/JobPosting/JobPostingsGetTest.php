<?php

namespace App\JobPosting\Tests\Functional\Turnover\JobPosting;

use App\JobPosting\Entity\JobPostingSearchRecruiterLog;
use App\Recruiter\Entity\Recruiter;
use App\Tests\Functional\ApiTestCase;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;

class JobPostingsGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();

        $client->request('GET', '/job_postings');

        self::assertResponseStatusCodeSame(401);
    }

    public static function provideLoggedCases(): iterable
    {
        yield [
            'filters' => [],
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
                        'candidateProfile' => null,
                        'companyDescription' => null,
                        'experienceLevel' => 'junior',
                        'minAnnualSalary' => 60000,
                        'maxAnnualSalary' => 60000,
                        'minDailySalary' => null,
                        'maxDailySalary' => null,
                        'currency' => 'USD',
                        'contracts' => [
                            'permanent',
                            'intercontract',
                        ],
                        'duration' => null,
                        'durationValue' => null,
                        'durationPeriod' => null,
                        'renewable' => false,
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
                        'startsAt' => '2022-03-21T00:00:00+01:00',
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
                                '@id' => '/skills/2',
                                '@type' => 'Skill',
                                'id' => 2,
                                'name' => 'java',
                                'slug' => 'java',
                            ],
                        ],
                        'publishedAt' => Carbon::yesterday()->setTime(8, 0)->format(\DateTimeInterface::RFC3339),
                        'published' => true,
                        'status' => 'published',
                        'softSkills' => [
                        ],
                        'annualSalary' => "60k\u{a0}\$US",
                        'dailySalary' => null,
                        'expiredAt' => Carbon::yesterday()->addDays(45)->setTime(8, 0)->format(\DateTimeInterface::RFC3339),
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
                'hydra:totalItems' => 4,
                'hydra:view' => [
                    '@id' => '/job_postings?page=1',
                    '@type' => 'hydra:PartialCollectionView',
                    'hydra:first' => '/job_postings?page=1',
                    'hydra:last' => '/job_postings?page=2',
                    'hydra:next' => '/job_postings?page=2',
                ],
            ],
        ];

        yield [
            'filters' => [
                'minDuration' => 4,
            ],
            'expected' => [
                '@context' => '/contexts/JobPosting',
                '@id' => '/job_postings',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    [
                        '@id' => '/job_postings/53',
                        '@type' => 'JobPosting',
                        'id' => 53,
                        'title' => 'Développeur NuxtJS (H/F)',
                        'duration' => 10,
                        'durationValue' => 10,
                        'durationPeriod' => 'month',
                    ],
                ],
                'hydra:totalItems' => 1,
                'hydra:view' => [
                    '@id' => '/job_postings?minDuration=4',
                    '@type' => 'hydra:PartialCollectionView',
                ],
            ],
        ];

        yield [
            'filters' => [
                'maxDuration' => 3,
            ],
            'expected' => [
                '@context' => '/contexts/JobPosting',
                '@id' => '/job_postings',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    [
                        '@id' => '/job_postings/54',
                        '@type' => 'JobPosting',
                        'id' => 54,
                        'title' => 'Développeur Zend 10 ans expériences  (H/F)',
                        'duration' => 3,
                        'durationValue' => 3,
                        'durationPeriod' => 'month',
                    ],
                ],
                'hydra:totalItems' => 1,
                'hydra:view' => [
                    '@id' => '/job_postings?maxDuration=3',
                    '@type' => 'hydra:PartialCollectionView',
                ],
            ],
        ];

        yield [
            'filters' => [
                'locations' => 'fr~ile-de-france,fr~hauts-de-france~pas-de-calais~le-touquet-paris-plage',
            ],
            'expected' => [
                '@context' => '/contexts/JobPosting',
                '@id' => '/job_postings',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    [
                        '@id' => '/job_postings/54',
                        '@type' => 'JobPosting',
                        'id' => 54,
                        'title' => 'Développeur Zend 10 ans expériences  (H/F)',
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
                    ],
                    [
                        '@id' => '/job_postings/51',
                        '@type' => 'JobPosting',
                        'id' => 51,
                        'title' => 'Développeur Java 8 ans Exp - IDF - (H/F)',
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
                    ],
                ],
                'hydra:totalItems' => 3,
                'hydra:view' => [
                    '@id' => '/job_postings?locations=fr~ile-de-france%2Cfr~hauts-de-france~pas-de-calais~le-touquet-paris-plage&page=1',
                    '@type' => 'hydra:PartialCollectionView',
                    'hydra:first' => '/job_postings?locations=fr~ile-de-france%2Cfr~hauts-de-france~pas-de-calais~le-touquet-paris-plage&page=1',
                    'hydra:last' => '/job_postings?locations=fr~ile-de-france%2Cfr~hauts-de-france~pas-de-calais~le-touquet-paris-plage&page=2',
                    'hydra:next' => '/job_postings?locations=fr~ile-de-france%2Cfr~hauts-de-france~pas-de-calais~le-touquet-paris-plage&page=2',
                ],
            ],
        ];

        yield [
            'filters' => [
                'locations' => 'fr~hauts-de-france~pas-de-calais~le-touquet-paris-plage',
            ],
            'expected' => [
                '@context' => '/contexts/JobPosting',
                '@id' => '/job_postings',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    [
                        '@id' => '/job_postings/54',
                        '@type' => 'JobPosting',
                        'id' => 54,
                        'title' => 'Développeur Zend 10 ans expériences  (H/F)',
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
                    ],
                ],
                'hydra:totalItems' => 1,
                'hydra:view' => [
                    '@id' => '/job_postings?locations=fr~hauts-de-france~pas-de-calais~le-touquet-paris-plage',
                    '@type' => 'hydra:PartialCollectionView',
                ],
            ],
        ];

        yield [
            'filters' => [
                'minDailySalary' => 600,
            ],
            'expected' => [
                '@context' => '/contexts/JobPosting',
                '@id' => '/job_postings',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    [
                        '@id' => '/job_postings/54',
                        '@type' => 'JobPosting',
                        'id' => 54,
                        'title' => 'Développeur Zend 10 ans expériences  (H/F)',
                        'minDailySalary' => 850,
                        'maxDailySalary' => 1000,
                    ],
                    [
                        '@id' => '/job_postings/52',
                        '@type' => 'JobPosting',
                        'id' => 52,
                        'title' => 'Développeur Javascript 5 ans Exp - IDF - (H/F)',
                        'minDailySalary' => 500,
                        'maxDailySalary' => 800,
                    ],
                ],
                'hydra:totalItems' => 2,
                'hydra:view' => [
                    '@id' => '/job_postings?minDailySalary=600',
                    '@type' => 'hydra:PartialCollectionView',
                ],
            ],
        ];

        yield [
            'filters' => [
                'maxDailySalary' => 800,
            ],
            'expected' => [
                '@context' => '/contexts/JobPosting',
                '@id' => '/job_postings',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    [
                        '@id' => '/job_postings/53',
                        '@type' => 'JobPosting',
                        'id' => 53,
                        'title' => 'Développeur NuxtJS (H/F)',
                        'minDailySalary' => 500,
                        'maxDailySalary' => 550,
                    ],
                    [
                        '@id' => '/job_postings/52',
                        '@type' => 'JobPosting',
                        'id' => 52,
                        'title' => 'Développeur Javascript 5 ans Exp - IDF - (H/F)',
                        'minDailySalary' => 500,
                        'maxDailySalary' => 800,
                    ],
                ],
                'hydra:totalItems' => 2,
                'hydra:view' => [
                    '@id' => '/job_postings?maxDailySalary=800',
                    '@type' => 'hydra:PartialCollectionView',
                ],
            ],
        ];

        yield [
            'filters' => [
                'remoteMode' => 'full',
            ],
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
                        'remoteMode' => 'full',
                    ],
                    [
                        '@id' => '/job_postings/52',
                        '@type' => 'JobPosting',
                        'id' => 52,
                        'title' => 'Développeur Javascript 5 ans Exp - IDF - (H/F)',
                        'remoteMode' => 'full',
                    ],
                ],
                'hydra:totalItems' => 2,
                'hydra:view' => [
                    '@id' => '/job_postings?remoteMode=full',
                    '@type' => 'hydra:PartialCollectionView',
                ],
            ],
        ];

        yield [
            'filters' => [
                'keywords' => 'Zend',
            ],
            'expected' => [
                '@context' => '/contexts/JobPosting',
                '@id' => '/job_postings',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    [
                        '@id' => '/job_postings/54',
                        '@type' => 'JobPosting',
                        'id' => 54,
                        'title' => 'Développeur Zend 10 ans expériences  (H/F)',
                    ],
                ],
                'hydra:totalItems' => 1,
                'hydra:view' => [
                    '@id' => '/job_postings?keywords=Zend',
                    '@type' => 'hydra:PartialCollectionView',
                ],
            ],
        ];

        yield [
            'filters' => [
                'businessActivity' => 'business-activity-1',
            ],
            'expected' => [
                '@context' => '/contexts/JobPosting',
                '@id' => '/job_postings',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    [
                        '@id' => '/job_postings/54',
                        '@type' => 'JobPosting',
                        'id' => 54,
                        'title' => 'Développeur Zend 10 ans expériences  (H/F)',
                    ],
                    [
                        '@id' => '/job_postings/53',
                        '@type' => 'JobPosting',
                        'id' => 53,
                        'title' => 'Développeur NuxtJS (H/F)',
                    ],
                ],
                'hydra:totalItems' => 2,
                'hydra:view' => [
                    '@id' => '/job_postings?businessActivity=business-activity-1',
                    '@type' => 'hydra:PartialCollectionView',
                ],
            ],
        ];

        yield [
            'filters' => [
                'intercontractOnly' => 'true',
            ],
            'expected' => [
                '@context' => '/contexts/JobPosting',
                '@id' => '/job_postings',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    [
                        '@id' => '/job_postings/54',
                        '@type' => 'JobPosting',
                        'id' => 54,
                        'title' => 'Développeur Zend 10 ans expériences  (H/F)',
                        'contracts' => [
                            'intercontract',
                        ],
                    ],
                    [
                        '@id' => '/job_postings/53',
                        '@type' => 'JobPosting',
                        'id' => 53,
                        'title' => 'Développeur NuxtJS (H/F)',
                        'contracts' => [
                            'intercontract',
                        ],
                    ],
                ],
                'hydra:totalItems' => 3,
                'hydra:view' => [
                    '@id' => '/job_postings?intercontractOnly=true&page=1',
                    '@type' => 'hydra:PartialCollectionView',
                    'hydra:first' => '/job_postings?intercontractOnly=true&page=1',
                    'hydra:last' => '/job_postings?intercontractOnly=true&page=2',
                    'hydra:next' => '/job_postings?intercontractOnly=true&page=2',
                ],
            ],
        ];

        yield [
            'filters' => [
                'intercontractOnly' => 'false',
            ],
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
                        'contracts' => [
                            'permanent',
                            'intercontract',
                        ],
                    ],
                    [
                        '@id' => '/job_postings/54',
                        '@type' => 'JobPosting',
                        'id' => 54,
                        'title' => 'Développeur Zend 10 ans expériences  (H/F)',
                        'contracts' => [
                            'intercontract',
                        ],
                    ],
                ],
                'hydra:totalItems' => 4,
                'hydra:view' => [
                    '@id' => '/job_postings?intercontractOnly=false&page=1',
                    '@type' => 'hydra:PartialCollectionView',
                    'hydra:first' => '/job_postings?intercontractOnly=false&page=1',
                    'hydra:last' => '/job_postings?intercontractOnly=false&page=2',
                    'hydra:next' => '/job_postings?intercontractOnly=false&page=2',
                ],
            ],
        ];

        yield [
            'filters' => [
                'order' => 'relevance',
                'keywords' => 'java',
            ],
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
                    ],
                    [
                        '@id' => '/job_postings/54',
                        '@type' => 'JobPosting',
                        'id' => 54,
                        'title' => 'Développeur Zend 10 ans expériences  (H/F)',
                    ],
                ],
                'hydra:totalItems' => 4,
                'hydra:view' => [
                    '@id' => '/job_postings?order=relevance&keywords=java&page=1',
                    '@type' => 'hydra:PartialCollectionView',
                    'hydra:first' => '/job_postings?order=relevance&keywords=java&page=1',
                    'hydra:last' => '/job_postings?order=relevance&keywords=java&page=2',
                    'hydra:next' => '/job_postings?order=relevance&keywords=java&page=2',
                ],
            ],
        ];

        yield [
            'filters' => [
                'order' => 'date',
            ],
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
                        'publishedAt' => Carbon::yesterday()->setTime(8, 0)->format(\DateTimeInterface::RFC3339),
                    ],
                    [
                        '@id' => '/job_postings/53',
                        '@type' => 'JobPosting',
                        'id' => 53,
                        'title' => 'Développeur NuxtJS (H/F)',
                        'publishedAt' => '2022-09-17T21:34:41+02:00',
                    ],
                ],
                'hydra:totalItems' => 4,
                'hydra:view' => [
                    '@id' => '/job_postings?order=date&page=1',
                    '@type' => 'hydra:PartialCollectionView',
                    'hydra:first' => '/job_postings?order=date&page=1',
                    'hydra:last' => '/job_postings?order=date&page=2',
                    'hydra:next' => '/job_postings?order=date&page=2',
                ],
            ],
        ];

        yield [
            'filters' => [
                'order' => 'salary',
            ],
            'expected' => [
                '@context' => '/contexts/JobPosting',
                '@id' => '/job_postings',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    [
                        '@id' => '/job_postings/53',
                        '@type' => 'JobPosting',
                        'id' => 53,
                        'title' => 'Développeur NuxtJS (H/F)',
                        'minDailySalary' => 500,
                        'maxDailySalary' => 550,
                    ],
                    [
                        '@id' => '/job_postings/52',
                        '@type' => 'JobPosting',
                        'id' => 52,
                        'title' => 'Développeur Javascript 5 ans Exp - IDF - (H/F)',
                        'minDailySalary' => 500,
                        'maxDailySalary' => 800,
                    ],
                ],
                'hydra:totalItems' => 4,
                'hydra:view' => [
                    '@id' => '/job_postings?order=salary&page=1',
                    '@type' => 'hydra:PartialCollectionView',
                    'hydra:first' => '/job_postings?order=salary&page=1',
                    'hydra:last' => '/job_postings?order=salary&page=2',
                    'hydra:next' => '/job_postings?order=salary&page=2',
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideLoggedCases
     */
    public function testLogged(array $filters, array $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient('jesse.pinkman@breaking-bad.com');

        $client->request('GET', '/job_postings?' . http_build_query($filters));

        self::assertResponseIsSuccessful();
        self::assertJsonContains($expected);
    }

    public static function provideCompleteCases(): iterable
    {
        yield [
            'filters' => [
                'businessActivity' => 'business-activity-1',
                'intercontractOnly' => 'true',
                'keywords' => 'java',
                'order' => 'salary',
                'remoteMode' => 'full',
                'minDailySalary' => 600,
                'maxDailySalary' => 800,
                'locations' => 'fr~ile-de-france~~paris,fr~hauts-de-france~~le-touquet-paris-plage',
                'minDuration' => 4,
                'maxDuration' => 3,
            ],
            'expected' => [
                '@context' => '/contexts/JobPosting',
                '@id' => '/job_postings',
                '@type' => 'hydra:Collection',
                'hydra:member' => [],
                'hydra:totalItems' => 0,
            ],
        ];
    }

    /**
     * @dataProvider provideCompleteCases
     */
    public function testComplete(array $filters, array $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient('jesse.pinkman@breaking-bad.com');

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();

        // 1 - before
        $recruiter = $em->getRepository(Recruiter::class)->findOneBy([
            'email' => 'jesse.pinkman@breaking-bad.com',
        ]);
        self::assertNotNull($recruiter);
        $logs = $em->getRepository(JobPostingSearchRecruiterLog::class)->findBy([
            'recruiter' => $recruiter,
        ]);
        self::assertCount(1, $logs);

        // 2 - get
        $client->request('GET', '/job_postings?' . http_build_query($filters));

        // 3 - after
        $recruiter = $em->getRepository(Recruiter::class)->findOneBy([
            'email' => 'jesse.pinkman@breaking-bad.com',
        ]);
        self::assertNotNull($recruiter);
        $logs = $em->getRepository(JobPostingSearchRecruiterLog::class)->findBy([
            'recruiter' => $recruiter,
        ]);
        self::assertCount(2, $logs);
        /** @var JobPostingSearchRecruiterLog $log */
        $log = end($logs);
        self::assertSame('business-activity-1', $log->getBusinessActivity()->getSlug());
        self::assertTrue($log->getIntercontractOnly());
        self::assertSame('java', $log->getKeywords());
        self::assertSame(['full'], $log->getRemoteMode());
        self::assertSame(600, $log->getMinDailySalary());
        self::assertSame(800, $log->getMaxDailySalary());
        self::assertSame(4, $log->getMinDuration());
        self::assertSame(3, $log->getMaxDuration());
        self::assertSame(2, $log->getLocations()->count());

        self::assertResponseIsSuccessful();
        self::assertJsonContains($expected);
    }
}
