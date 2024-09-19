<?php

namespace App\JobPosting\Tests\Functional\Turnover\JobPostingTemplate;

use App\Tests\Functional\ApiTestCase;

class JobPostingTemplateGetCollectionTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/job_posting_templates?itemsPerPage=1');

        self::assertResponseStatusCodeSame(401);
        self::assertJsonContains(
            [
                '@context' => '/contexts/Error',
                '@type' => 'hydra:Error',
                'hydra:title' => 'An error occurred',
                'hydra:description' => '',
            ]
        );
    }

    public function testNotFound(): void
    {
        $client = static::createTurnoverAuthenticatedClient('eddard.stark@got.com');
        $client->request('GET', '/job_posting_templates/not-fount');

        self::assertResponseStatusCodeSame(404);
        self::assertJsonContains(
            [
                '@context' => '/contexts/Error',
                '@type' => 'hydra:Error',
                'hydra:title' => 'An error occurred',
                'hydra:description' => 'Not Found',
            ]
        );
    }

    public static function provideLoggedCases(): iterable
    {
        $gotExpected = [
            '@context' => '/contexts/JobPostingTemplate',
            '@id' => '/job_posting_templates',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/job_posting_templates/1',
                    '@type' => 'JobPostingTemplate',
                    'id' => 1,
                    'createdBy' => [
                        '@id' => '/recruiters/4',
                        '@type' => 'Recruiter',
                        'id' => 4,
                        'email' => 'eddard.stark@got.com',
                        'firstName' => 'Eddard',
                        'lastName' => 'Stark',
                        'company' => '/companies/company-2',
                    ],
                    'title' => 'Développeur Web',
                    'contracts' => [
                        0 => 'permanent',
                    ],
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
                    ],
                    'minAnnualSalary' => 45000,
                    'maxAnnualSalary' => 55000,
                    'minDailySalary' => null,
                    'maxDailySalary' => null,
                    'currency' => 'EUR',
                    'durationValue' => 1,
                    'durationPeriod' => 'day',
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
                            '@id' => '/soft_skills/2',
                            '@type' => 'SoftSkill',
                            'id' => 2,
                            'name' => 'SoftSkill 2',
                            'slug' => 'softskill-2',
                        ],
                    ],
                    'applicationType' => 'turnover',
                    'applicationEmail' => null,
                    'applicationContact' => null,
                    'createdAt' => '2022-01-01T12:00:00+01:00',
                    'updatedAt' => '2022-01-01T12:00:00+01:00',
                    'dailySalary' => null,
                    'annualSalary' => "45k\u{a0}€",
                ],
                [
                    '@id' => '/job_posting_templates/2',
                    '@type' => 'JobPostingTemplate',
                    'id' => 2,
                    'createdBy' => [
                        '@id' => '/recruiters/4',
                        '@type' => 'Recruiter',
                        'id' => 4,
                        'email' => 'eddard.stark@got.com',
                        'firstName' => 'Eddard',
                        'lastName' => 'Stark',
                        'company' => '/companies/company-2',
                    ],
                    'title' => 'Lead développeur',
                    'contracts' => [
                        0 => 'intercontract',
                    ],
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
                    ],
                    'minAnnualSalary' => null,
                    'maxAnnualSalary' => null,
                    'minDailySalary' => 250,
                    'maxDailySalary' => 400,
                    'currency' => 'USD',
                    'durationValue' => 45,
                    'durationPeriod' => 'month',
                    'skills' => [],
                    'softSkills' => [],
                    'applicationType' => 'turnover',
                    'applicationEmail' => null,
                    'applicationContact' => null,
                    'createdAt' => '2022-01-01T13:00:00+01:00',
                    'updatedAt' => '2022-01-01T13:00:00+01:00',
                    'dailySalary' => "250\u{a0}\$US",
                    'annualSalary' => null,
                ],
            ],
            'hydra:totalItems' => 4,
            'hydra:view' => [
                '@id' => '/job_posting_templates?title=asc&page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/job_posting_templates?title=asc&page=1',
                'hydra:last' => '/job_posting_templates?title=asc&page=2',
                'hydra:next' => '/job_posting_templates?title=asc&page=2',
            ],
        ];

        yield [
            'recruiterEmail' => 'eddard.stark@got.com',
            'expected' => $gotExpected,
        ];

        yield [
            'recruiterEmail' => 'arya.stark@got.com',
            'expected' => $gotExpected,
        ];

        yield [
            'recruiterEmail' => 'carrie.mathison@homeland.com',
            'expected' => [
                '@context' => '/contexts/JobPostingTemplate',
                '@id' => '/job_posting_templates',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    [
                        '@id' => '/job_posting_templates/5',
                        '@type' => 'JobPostingTemplate',
                        'id' => 5,
                        'createdBy' => [
                            '@id' => '/recruiters/14',
                            '@type' => 'Recruiter',
                            'id' => 14,
                            'email' => 'carrie.mathison@homeland.com',
                            'firstName' => 'Carrie',
                            'lastName' => 'Mathison',
                            'company' => '/companies/company-4',
                        ],
                        'title' => 'Consultant Reflex',
                        'contracts' => [
                            0 => 'fixed-term',
                        ],
                        'location' => [
                            '@type' => 'Location',
                            'street' => null,
                            'locality' => null,
                            'postalCode' => null,
                            'adminLevel1' => 'Île-de-France',
                            'adminLevel2' => null,
                            'country' => 'France',
                            'countryCode' => 'FR',
                            'latitude' => '48.6443057',
                            'longitude' => '2.7537863',
                        ],
                        'minAnnualSalary' => 35000,
                        'maxAnnualSalary' => 40000,
                        'minDailySalary' => null,
                        'maxDailySalary' => null,
                        'currency' => 'GBP',
                        'durationValue' => 1,
                        'durationPeriod' => 'year',
                        'skills' => [
                            [
                                '@id' => '/skills/1',
                                '@type' => 'Skill',
                                'id' => 1,
                                'name' => 'php',
                                'slug' => 'php',
                            ],
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
                            [
                                '@id' => '/skills/7',
                                '@type' => 'Skill',
                                'id' => 7,
                                'name' => 'docker',
                                'slug' => 'docker',
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
                                '@id' => '/soft_skills/2',
                                '@type' => 'SoftSkill',
                                'id' => 2,
                                'name' => 'SoftSkill 2',
                                'slug' => 'softskill-2',
                            ],
                        ],
                        'applicationType' => 'turnover',
                        'applicationEmail' => null,
                        'applicationContact' => null,
                        'createdAt' => '2022-01-01T16:00:00+01:00',
                        'updatedAt' => '2022-01-01T16:00:00+01:00',
                        'dailySalary' => null,
                        'annualSalary' => "35k\u{a0}£GB",
                    ],
                    [
                        '@id' => '/job_posting_templates/6',
                        '@type' => 'JobPostingTemplate',
                        'id' => 6,
                        'createdBy' => [
                            '@id' => '/recruiters/14',
                            '@type' => 'Recruiter',
                            'id' => 14,
                            'email' => 'carrie.mathison@homeland.com',
                            'firstName' => 'Carrie',
                            'lastName' => 'Mathison',
                            'company' => '/companies/company-4',
                        ],
                        'title' => 'Administrateur BDD',
                        'contracts' => [
                            0 => 'permanent',
                        ],
                        'location' => [
                            '@type' => 'Location',
                            'street' => null,
                            'locality' => null,
                            'postalCode' => null,
                            'adminLevel1' => 'Île-de-France',
                            'adminLevel2' => null,
                            'country' => 'France',
                            'countryCode' => 'FR',
                            'latitude' => '48.6443057',
                            'longitude' => '2.7537863',
                        ],
                        'minAnnualSalary' => 45000,
                        'maxAnnualSalary' => null,
                        'minDailySalary' => null,
                        'maxDailySalary' => null,
                        'currency' => 'EUR',
                        'durationValue' => 36,
                        'durationPeriod' => 'month',
                        'skills' => [
                            [
                                '@id' => '/skills/1',
                                '@type' => 'Skill',
                                'id' => 1,
                                'name' => 'php',
                                'slug' => 'php',
                            ],
                            [
                                '@id' => '/skills/4',
                                '@type' => 'Skill',
                                'id' => 4,
                                'name' => 'symfony',
                                'slug' => 'symfony',
                            ],
                        ],
                        'softSkills' => [],
                        'applicationType' => 'turnover',
                        'applicationEmail' => null,
                        'applicationContact' => null,
                        'createdAt' => '2022-01-01T17:00:00+01:00',
                        'updatedAt' => '2022-01-01T17:00:00+01:00',
                        'dailySalary' => null,
                        'annualSalary' => "45k\u{a0}€",
                    ],
                ],
                'hydra:totalItems' => 2,
                'hydra:view' => [
                    '@id' => '/job_posting_templates?title=asc',
                    '@type' => 'hydra:PartialCollectionView',
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideLoggedCases
     */
    public function testLogged(string $recruiterEmail, array $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient($recruiterEmail);
        $client->request('GET', '/job_posting_templates?title=asc');

        self::assertResponseIsSuccessful();
        self::assertJsonContains($expected);
    }
}
