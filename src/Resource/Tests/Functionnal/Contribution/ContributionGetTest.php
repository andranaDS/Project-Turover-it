<?php

namespace App\Resource\Tests\Functionnal\Contribution;

use App\Tests\Functional\ApiTestCase;

class ContributionGetTest extends ApiTestCase
{
    public static function provideLoggedAndNotLoggedCases(): iterable
    {
        yield ['user@free-work.fr'];
        yield ['admin@free-work.fr'];
        yield [null];
    }

    /**
     * @dataProvider provideLoggedAndNotLoggedCases
     */
    public function testLoggedAndNotLogged(?string $email): void
    {
        if (null !== $email) {
            $client = self::createFreeWorkAuthenticatedClient($email);
        } else {
            $client = self::createFreeWorkClient();
        }

        $client->request('GET', '/contributions');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public static function provideWithContractFilterCases(): iterable
    {
        yield [
            'contractor',
            [
                '@context' => '/contexts/Contribution',
                '@id' => '/contributions',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    [
                        '@id' => '/contributions/20000',
                        '@type' => 'Contribution',
                        'id' => 20000,
                        'job' => [
                            '@id' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase',
                            '@type' => 'Job',
                            'id' => 1,
                            'name' => 'Administrateur BDD',
                        ],
                        'userCompanyStatus' => 'salary_portage',
                        'contract' => 'contractor',
                        'location' => 'outside_france',
                        'experienceYear' => 'more_than_15_years',
                        'employer' => 'recruitment_agency',
                        'foundBy' => 'intermediary',
                        'onCall' => false,
                        'annualSalary' => null,
                        'formattedAnnualSalary' => null,
                        'variableAnnualSalary' => null,
                        'formattedVariableAnnualSalary' => null,
                        'dailySalary' => 1000,
                        'formattedDailySalary' => "1k\u{a0}€",
                        'remoteDaysPerWeek' => 5,
                        'contractDuration' => 260,
                        'searchJobDuration' => 6,
                        'isFreelance' => true,
                    ],
                    [
                        '@id' => '/contributions/19999',
                        '@type' => 'Contribution',
                        'id' => 19999,
                        'job' => [
                            '@id' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase',
                            '@type' => 'Job',
                            'id' => 1,
                            'name' => 'Administrateur BDD',
                        ],
                        'userCompanyStatus' => 'salary_portage',
                        'contract' => 'contractor',
                        'location' => 'outside_france',
                        'experienceYear' => 'more_than_15_years',
                        'employer' => 'recruitment_agency',
                        'foundBy' => 'intermediary',
                        'onCall' => false,
                        'annualSalary' => null,
                        'formattedAnnualSalary' => null,
                        'variableAnnualSalary' => null,
                        'formattedVariableAnnualSalary' => null,
                        'dailySalary' => 1000,
                        'formattedDailySalary' => "1k\u{a0}€",
                        'remoteDaysPerWeek' => 5,
                        'contractDuration' => 260,
                        'searchJobDuration' => 6,
                        'isFreelance' => true,
                    ],
                ],
                'hydra:totalItems' => 10000,
            ],
        ];
    }

    /**
     * @dataProvider provideWithContractFilterCases
     */
    public function testWithContractFilter(string $filterValue, $expected): void
    {
        $client = self::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/contributions?contract=' . $filterValue);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains($expected);
    }

    public static function providePreviousAndNextContributionCases(): iterable
    {
        return [
            'no previous' => [
                1,
                [
                    'contribution' => [
                        '@context' => '/contexts/Contribution',
                        '@id' => '/contributions/1',
                        '@type' => 'Contribution',
                        'id' => 1,
                        'job' => [
                            '@id' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase',
                            '@type' => 'Job',
                            'id' => 1,
                            'name' => 'Administrateur BDD',
                        ],
                        'userCompanyStatus' => null,
                        'contract' => 'permanent',
                        'location' => 'ile_de_france',
                        'experienceYear' => 'less_than_1_year',
                        'employer' => 'final_client',
                        'foundBy' => 'freework',
                        'onCall' => true,
                        'annualSalary' => 32000,
                        'formattedAnnualSalary' => "32k\u{a0}€",
                        'variableAnnualSalary' => 0,
                        'formattedVariableAnnualSalary' => "0\u{a0}€",
                        'dailySalary' => null,
                        'formattedDailySalary' => null,
                        'remoteDaysPerWeek' => 0,
                        'contractDuration' => 5,
                        'searchJobDuration' => 0,
                        'isFreelance' => false,
                    ],
                    'previousContribution' => null,
                    'nextContribution' => [
                        '@context' => '/contexts/Contribution',
                        '@id' => '/contributions/2',
                        '@type' => 'Contribution',
                        'id' => 2,
                        'job' => [
                            '@id' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase',
                            '@type' => 'Job',
                            'id' => 1,
                            'name' => 'Administrateur BDD',
                        ],
                        'userCompanyStatus' => null,
                        'contract' => 'permanent',
                        'location' => 'ile_de_france',
                        'experienceYear' => 'less_than_1_year',
                        'employer' => 'final_client',
                        'foundBy' => 'freework',
                        'onCall' => true,
                        'annualSalary' => 32000,
                        'formattedAnnualSalary' => "32k\u{a0}€",
                        'variableAnnualSalary' => 0,
                        'formattedVariableAnnualSalary' => "0\u{a0}€",
                        'dailySalary' => null,
                        'formattedDailySalary' => null,
                        'remoteDaysPerWeek' => 0,
                        'contractDuration' => 5,
                        'searchJobDuration' => 0,
                        'isFreelance' => false,
                    ],
                ],
            ],
            'with previous and next' => [
                10,
                [
                    'contribution' => [
                        '@context' => '/contexts/Contribution',
                        '@id' => '/contributions/10',
                        '@type' => 'Contribution',
                        'id' => 10,
                        'job' => [
                            '@id' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase',
                            '@type' => 'Job',
                            'id' => 1,
                            'name' => 'Administrateur BDD',
                        ],
                        'userCompanyStatus' => null,
                        'contract' => 'permanent',
                        'location' => 'ile_de_france',
                        'experienceYear' => 'less_than_1_year',
                        'employer' => 'final_client',
                        'foundBy' => 'freework',
                        'onCall' => true,
                        'annualSalary' => 32000,
                        'formattedAnnualSalary' => "32k\u{a0}€",
                        'variableAnnualSalary' => 0,
                        'formattedVariableAnnualSalary' => "0\u{a0}€",
                        'dailySalary' => null,
                        'formattedDailySalary' => null,
                        'remoteDaysPerWeek' => 0,
                        'contractDuration' => 5,
                        'searchJobDuration' => 0,
                        'isFreelance' => false,
                    ],
                    'previousContribution' => [
                        '@context' => '/contexts/Contribution',
                        '@id' => '/contributions/9',
                        '@type' => 'Contribution',
                        'id' => 9,
                        'job' => [
                            '@id' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase',
                            '@type' => 'Job',
                            'id' => 1,
                            'name' => 'Administrateur BDD',
                        ],
                        'userCompanyStatus' => null,
                        'contract' => 'permanent',
                        'location' => 'ile_de_france',
                        'experienceYear' => 'less_than_1_year',
                        'employer' => 'final_client',
                        'foundBy' => 'freework',
                        'onCall' => true,
                        'annualSalary' => 32000,
                        'formattedAnnualSalary' => "32k\u{a0}€",
                        'variableAnnualSalary' => 0,
                        'formattedVariableAnnualSalary' => "0\u{a0}€",
                        'remoteDaysPerWeek' => 0,
                        'contractDuration' => 5,
                        'searchJobDuration' => 0,
                        'isFreelance' => false,
                    ],
                    'nextContribution' => [
                        '@context' => '/contexts/Contribution',
                        '@id' => '/contributions/11',
                        '@type' => 'Contribution',
                        'id' => 11,
                        'job' => [
                            '@id' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase',
                            '@type' => 'Job',
                            'id' => 1,
                            'name' => 'Administrateur BDD',
                        ],
                        'userCompanyStatus' => null,
                        'contract' => 'permanent',
                        'location' => 'ile_de_france',
                        'experienceYear' => 'less_than_1_year',
                        'employer' => 'final_client',
                        'foundBy' => 'freework',
                        'onCall' => true,
                        'annualSalary' => 32000,
                        'formattedAnnualSalary' => "32k\u{a0}€",
                        'variableAnnualSalary' => 0,
                        'formattedVariableAnnualSalary' => "0\u{a0}€",
                        'dailySalary' => null,
                        'formattedDailySalary' => null,
                        'remoteDaysPerWeek' => 0,
                        'contractDuration' => 5,
                        'searchJobDuration' => 0,
                        'isFreelance' => false,
                    ],
                ],
            ],
            'no next' => [
                20000,
                [
                    'contribution' => [
                        '@context' => '/contexts/Contribution',
                        '@id' => '/contributions/20000',
                        '@type' => 'Contribution',
                        'id' => 20000,
                        'job' => [
                            '@id' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase',
                            '@type' => 'Job',
                            'id' => 1,
                            'name' => 'Administrateur BDD',
                        ],
                        'userCompanyStatus' => 'salary_portage',
                        'contract' => 'contractor',
                        'location' => 'outside_france',
                        'experienceYear' => 'more_than_15_years',
                        'employer' => 'recruitment_agency',
                        'foundBy' => 'intermediary',
                        'onCall' => false,
                        'annualSalary' => null,
                        'formattedAnnualSalary' => null,
                        'formattedVariableAnnualSalary' => null,
                        'variableAnnualSalary' => null,
                        'dailySalary' => 1000,
                        'formattedDailySalary' => "1k\u{a0}€",
                        'remoteDaysPerWeek' => 5,
                        'contractDuration' => 260,
                        'searchJobDuration' => 6,
                        'isFreelance' => true,
                    ],
                    'previousContribution' => [
                        '@context' => '/contexts/Contribution',
                        '@id' => '/contributions/19999',
                        '@type' => 'Contribution',
                        'id' => 19999,
                        'job' => [
                            '@id' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase',
                            '@type' => 'Job',
                            'id' => 1,
                            'name' => 'Administrateur BDD',
                        ],
                        'userCompanyStatus' => 'salary_portage',
                        'contract' => 'contractor',
                        'location' => 'outside_france',
                        'experienceYear' => 'more_than_15_years',
                        'employer' => 'recruitment_agency',
                        'foundBy' => 'intermediary',
                        'onCall' => false,
                        'annualSalary' => null,
                        'formattedAnnualSalary' => null,
                        'variableAnnualSalary' => null,
                        'formattedVariableAnnualSalary' => null,
                        'dailySalary' => 1000,
                        'formattedDailySalary' => "1k\u{a0}€",
                        'remoteDaysPerWeek' => 5,
                        'contractDuration' => 260,
                        'searchJobDuration' => 6,
                        'isFreelance' => true,
                    ],
                    'nextContribution' => null,
                ],
            ],
        ];
    }

    /**
     * @@dataProvider  providePreviousAndNextContributionCases
     */
    public function testPreviousAndNextContribution(int $currentId, $expected): void
    {
        $client = self::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/contributions/' . $currentId);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains($expected);
    }

    public static function provideWithJobFilterCases(): iterable
    {
        yield [
            1,
            [
                '@context' => '/contexts/Contribution',
                '@id' => '/contributions',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    [
                        '@id' => '/contributions/20000',
                        '@type' => 'Contribution',
                        'id' => 20000,
                        'job' => [
                            '@id' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase',
                            '@type' => 'Job',
                            'id' => 1,
                            'name' => 'Administrateur BDD',
                        ],
                        'userCompanyStatus' => 'salary_portage',
                        'contract' => 'contractor',
                        'location' => 'outside_france',
                        'experienceYear' => 'more_than_15_years',
                        'employer' => 'recruitment_agency',
                        'foundBy' => 'intermediary',
                        'onCall' => false,
                        'annualSalary' => null,
                        'formattedAnnualSalary' => null,
                        'variableAnnualSalary' => null,
                        'formattedVariableAnnualSalary' => null,
                        'dailySalary' => 1000,
                        'formattedDailySalary' => "1k\u{a0}€",
                        'remoteDaysPerWeek' => 5,
                        'contractDuration' => 260,
                        'searchJobDuration' => 6,
                        'isFreelance' => true,
                    ],
                    [
                        '@id' => '/contributions/19999',
                        '@type' => 'Contribution',
                        'id' => 19999,
                        'job' => [
                            '@id' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase',
                            '@type' => 'Job',
                            'id' => 1,
                            'name' => 'Administrateur BDD',
                        ],
                        'userCompanyStatus' => 'salary_portage',
                        'contract' => 'contractor',
                        'location' => 'outside_france',
                        'experienceYear' => 'more_than_15_years',
                        'employer' => 'recruitment_agency',
                        'foundBy' => 'intermediary',
                        'onCall' => false,
                        'annualSalary' => null,
                        'formattedAnnualSalary' => null,
                        'variableAnnualSalary' => null,
                        'formattedVariableAnnualSalary' => null,
                        'dailySalary' => 1000,
                        'formattedDailySalary' => "1k\u{a0}€",
                        'remoteDaysPerWeek' => 5,
                        'contractDuration' => 260,
                        'searchJobDuration' => 6,
                        'isFreelance' => true,
                    ],
                ],
                'hydra:totalItems' => 20000,
            ],
        ];

        yield [
            2,
            [
                '@context' => '/contexts/Contribution',
                '@id' => '/contributions',
                '@type' => 'hydra:Collection',
                'hydra:member' => [],
                'hydra:totalItems' => 0,
            ],
        ];
    }

    /**
     * @dataProvider provideWithJobFilterCases
     */
    public function testWithJobFilter(int $filterValue, $expected): void
    {
        $client = self::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/contributions?job=' . $filterValue);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains($expected);
    }
}
