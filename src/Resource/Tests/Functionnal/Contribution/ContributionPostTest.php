<?php

namespace App\Resource\Tests\Functionnal\Contribution;

use App\Resource\Entity\Contribution;
use App\Tests\Functional\ApiTestCase;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;

class ContributionPostTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('POST', '/contributions');

        self::assertResponseStatusCodeSame(401);
    }

    public static function provideWithValidDataCases(): iterable
    {
        return [
            'free' => [
                [
                    'job' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase',
                    'contract' => 'contractor',
                    'location' => 'ile_de_france',
                    'experienceYear' => 'less_than_1_year',
                    'employer' => 'final_client',
                    'foundBy' => 'directly',
                    'onCall' => false,
                    'remoteDaysPerWeek' => 3,
                    'contractDuration' => 18,
                    'searchJobDuration' => 2,
                    'userCompanyStatus' => 'company',
                    'dailySalary' => 500,
                    'annualSalary' => 55000,
                    'variableAnnualSalary' => 500,
                ],
                [
                    'annualSalary' => null,
                    'formattedAnnualSalary' => null,
                    'formattedDailySalary' => "500\u{a0}€",
                    'variableAnnualSalary' => null,
                    'formattedVariableAnnualSalary' => null,
                    'job' => [
                        '@id' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase',
                        '@type' => 'Job',
                        'id' => 1,
                        'name' => 'Administrateur BDD',
                        'slug' => 'administrateur-bdd',
                    ],
                ],
            ],
            'work internship' => [
                [
                    'job' => '/jobs/administrateur-applicatif-erp-crm-sirh',
                    'contract' => 'internship',
                    'location' => 'large_cities',
                    'experienceYear' => '3-4_years',
                    'employer' => 'digital_service_company',
                    'foundBy' => 'freework',
                    'onCall' => true,
                    'remoteDaysPerWeek' => 3,
                    'contractDuration' => 18,
                    'searchJobDuration' => 5,
                    'annualSalary' => 55000,
                    'variableAnnualSalary' => 500,
                    'dailySalary' => 500,
                    'userCompanyStatus' => 'company',
                ],
                [
                    'formattedVariableAnnualSalary' => "500\u{a0}€",
                    'formattedAnnualSalary' => "55k\u{a0}€",
                    'dailySalary' => null,
                    'formattedDailySalary' => null,
                    'userCompanyStatus' => null,
                    'job' => [
                        '@id' => '/jobs/administrateur-applicatif-erp-crm-sirh',
                        '@type' => 'Job',
                        'id' => 2,
                        'name' => 'Administrateur ERP',
                        'slug' => 'administrateur-erp',
                    ],
                ],
            ],
            'work permanent' => [
                [
                    'job' => '/jobs/administrateur-systeme-linux',
                    'contract' => 'permanent',
                    'location' => 'small_cities',
                    'experienceYear' => '1-2_years',
                    'employer' => 'agency',
                    'foundBy' => 'freework',
                    'onCall' => true,
                    'remoteDaysPerWeek' => 0,
                    'searchJobDuration' => 8,
                    'annualSalary' => 75000,
                    'variableAnnualSalary' => 0,
                    'dailySalary' => 500,
                    'userCompanyStatus' => 'salary_portage',
                ],
                [
                    'formattedVariableAnnualSalary' => "0\u{a0}€",
                    'formattedAnnualSalary' => "75k\u{a0}€",
                    'dailySalary' => null,
                    'formattedDailySalary' => null,
                    'userCompanyStatus' => null,
                    'contractDuration' => null,
                    'job' => [
                        '@id' => '/jobs/administrateur-systeme-linux',
                        '@type' => 'Job',
                        'id' => 3,
                        'name' => 'Administrateur Linux',
                        'slug' => 'administrateur-linux',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideWithValidDataCases
     */
    public function testWithValidData(array $payload, array $fieldsChanged): void
    {
        $client = self::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('POST', '/contributions', [
            'json' => $payload,
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains(
            array_merge(
                [
                    '@context' => '/contexts/Contribution',
                    '@type' => 'Contribution',
                ],
                $payload,
                $fieldsChanged
            )
        );

        // Test createdBy field
        if (null === $container = $client->getContainer()) {
            throw new \RuntimeException('Container is null');
        }
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();
        $lastContribution = $em->getRepository(Contribution::class)->findOneBy([], ['id' => Criteria::DESC]);

        self::assertNotNull($lastContribution->getCreatedBy());
        self::assertSame($lastContribution->getCreatedBy()->getEmail(), 'claude.monet@free-work.fr');
    }

    public static function provideWithInvalidDataCases(): iterable
    {
        return [
            'free' => [
                [
                    'job' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase',
                    'contract' => 'contractor',
                    'location' => 'invalid_location',
                    'experienceYear' => 'less_than_1_year_too_long',
                    'employer' => 'invalid_employer',
                    'foundBy' => 'directly',
                    'onCall' => false,
                    'remoteDaysPerWeek' => 3,
                    'searchJobDuration' => 1,
                    'userCompanyStatus' => 'company',
                    'dailySalary' => 500,
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'location',
                            'message' => 'Cette valeur doit être l\'un des choix proposés.',
                        ],
                        [
                            'propertyPath' => 'experienceYear',
                            'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 24 caractères.',
                        ],
                        [
                            'propertyPath' => 'experienceYear',
                            'message' => 'Cette valeur doit être l\'un des choix proposés.',
                        ],
                        [
                            'propertyPath' => 'employer',
                            'message' => 'Cette valeur doit être l\'un des choix proposés.',
                        ],
                        [
                            'propertyPath' => 'contractDuration',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideWithInvalidDataCases
     */
    public function testWithInvalidData(array $payload, array $expected): void
    {
        $client = self::createFreeWorkAuthenticatedAdminClient();
        $client->request('POST', '/contributions', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains($expected);
    }

    public static function provideWithMissingDataCases(): iterable
    {
        return [
            'free' => [
                [],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'job',
                            'message' => 'Cette valeur ne doit pas être nulle.',
                        ],
                        [
                            'propertyPath' => 'contract',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'location',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'experienceYear',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'employer',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'foundBy',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'onCall',
                            'message' => 'Cette valeur ne doit pas être nulle.',
                        ],
                        [
                            'propertyPath' => 'remoteDaysPerWeek',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'contractDuration',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'searchJobDuration',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideWithMissingDataCases
     */
    public function testWithMissingData(array $payload, array $expected): void
    {
        $client = self::createFreeWorkAuthenticatedUserClient();
        $client->request('POST', '/contributions', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains($expected);
    }
}
