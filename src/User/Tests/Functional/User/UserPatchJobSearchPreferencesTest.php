<?php

namespace App\User\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;
use Symfony\Component\String\ByteString;

class UserPatchJobSearchPreferencesTest extends ApiTestCase
{
    public static function provideWithValidDataCases(): iterable
    {
        return [
            'status_in_progress' => [
                [
                    'jobs' => [
                        [
                            'job' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase',
                            'main' => true,
                        ],
                        [
                            'job' => '/jobs/administrateur-applicatif-erp-crm-sirh',
                            'main' => false,
                        ],
                        [
                            'job' => '/jobs/administrateur-systeme-linux',
                            'main' => true,
                        ],
                    ],
                    'fulltimeTeleworking' => false,
                    'employmentTime' => 'full_time',
                    'freelance' => true,
                    'freelanceLegalStatus' => 'status_in_progress',
                    'companyRegistrationNumberBeingAttributed' => true,
                    'averageDailyRate' => 500,
                    'freelanceCurrency' => 'EUR',
                    'employee' => true,
                    'grossAnnualSalary' => 50000,
                    'employeeCurrency' => 'EUR',
                    'contracts' => [
                        'permanent',
                        'apprenticeship',
                        'fixed-term',
                    ],
                    'locationKeys' => [
                        'fr~hauts-de-france~~le-touquet-paris-plage',
                        'fr~auvergne-rhone-alpes~seyssinet-pariset',
                    ],
                ],
                [
                    '@context' => '/contexts/User',
                    '@type' => 'User',
                    'jobs' => [
                        [
                            '@type' => 'UserJob',
                            'job' => [
                                '@type' => 'Job',
                                'name' => 'Administrateur BDD',
                            ],
                            'main' => true,
                        ],
                        [
                            '@type' => 'UserJob',
                            'job' => [
                                '@type' => 'Job',
                                'name' => 'Administrateur ERP',
                            ],
                            'main' => false,
                        ],
                        [
                            '@type' => 'UserJob',
                            'job' => [
                                '@type' => 'Job',
                                'name' => 'Administrateur Linux',
                            ],
                            'main' => true,
                        ],
                    ],
                    'fulltimeTeleworking' => false,
                    'employmentTime' => 'full_time',
                    'freelance' => true,
                    'freelanceLegalStatus' => 'status_in_progress',
                    'companyRegistrationNumberBeingAttributed' => true,
                    'averageDailyRate' => 500,
                    'freelanceCurrency' => 'EUR',
                    'employee' => true,
                    'grossAnnualSalary' => 50000,
                    'employeeCurrency' => 'EUR',
                    'contracts' => [
                        'permanent',
                        'apprenticeship',
                        'fixed-term',
                    ],
                    'locations' => [
                        [
                            '@type' => 'UserMobility',
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
                            '@type' => 'UserMobility',
                            'location' => [
                                '@type' => 'Location',
                                'street' => null,
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
                        ],
                    ],
                    'formStep' => 'job_search_preferences',
                    'partner' => [
                        'id' => 4,
                    ],
                ],
            ],
            'umbrella_company' => [
                [
                    'jobs' => [
                        [
                            'job' => '/jobs/administrateur-systeme-linux',
                            'main' => false,
                        ],
                    ],
                    'fulltimeTeleworking' => true,
                    'employmentTime' => 'part_time',
                    'freelance' => true,
                    'freelanceLegalStatus' => 'umbrella_company',
                    'umbrellaCompany' => [
                        'name' => 'Umbrella Company Name',
                    ],
                    'companyRegistrationNumber' => '123456789',
                    'companyCountryCode' => 'AT',
                    'averageDailyRate' => 500,
                    'freelanceCurrency' => 'USD',
                    'employee' => false,
                    'contracts' => [],
                    'locationKeys' => [
                        'fr~hauts-de-france~~le-touquet-paris-plage',
                    ],
                ],
                [
                    '@context' => '/contexts/User',
                    '@type' => 'User',
                    'jobs' => [
                        [
                            '@type' => 'UserJob',
                            'job' => [
                                '@type' => 'Job',
                                'name' => 'Administrateur Linux',
                                'slug' => 'administrateur-linux',
                            ],
                            'main' => false,
                        ],
                    ],
                    'fulltimeTeleworking' => true,
                    'employmentTime' => 'part_time',
                    'freelance' => true,
                    'freelanceLegalStatus' => 'umbrella_company',
                    'umbrellaCompany' => [
                        '@type' => 'UmbrellaCompany',
                        'name' => 'Umbrella Company Name',
                    ],
                    'companyRegistrationNumber' => '123456789',
                    'companyCountryCode' => 'AT',
                    'averageDailyRate' => 500,
                    'freelanceCurrency' => 'USD',
                    'employee' => false,
                    'contracts' => [],
                    'locations' => [
                        [
                            '@type' => 'UserMobility',
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
                    'formStep' => 'job_search_preferences',
                ],
            ],
            'insurance_company' => [
                [
                    'jobs' => [
                        [
                            'job' => '/jobs/administrateur-systeme-linux',
                            'main' => false,
                        ],
                    ],
                    'fulltimeTeleworking' => true,
                    'employmentTime' => 'part_time',
                    'freelance' => true,
                    'freelanceLegalStatus' => 'sas_sasu',
                    'insurance' => true,
                    'insuranceNumber' => 'PER10AMP788',
                    'insuranceExpiredAt' => '2025-01-01T00:00:00+00:00',
                    'insuranceCompany' => [
                        'name' => 'Insurance Company Name',
                    ],
                    'nafCode' => '6202b',
                    'companyRegistrationNumberBeingAttributed' => true,
                    'averageDailyRate' => 500,
                    'freelanceCurrency' => 'USD',
                    'employee' => false,
                    'contracts' => [],
                    'locationKeys' => [
                        'fr~hauts-de-france~~le-touquet-paris-plage',
                    ],
                ],
                [
                    '@context' => '/contexts/User',
                    '@type' => 'User',
                    'jobs' => [
                        [
                            '@type' => 'UserJob',
                            'job' => [
                                '@type' => 'Job',
                                'name' => 'Administrateur Linux',
                                'slug' => 'administrateur-linux',
                            ],
                            'main' => false,
                        ],
                    ],
                    'fulltimeTeleworking' => true,
                    'employmentTime' => 'part_time',
                    'freelance' => true,
                    'freelanceLegalStatus' => 'sas_sasu',
                    'umbrellaCompany' => null,
                    'insuranceNumber' => 'PER10AMP788',
                    'insuranceExpiredAt' => '2025-01-01T00:00:00+01:00',
                    'insuranceCompany' => [
                        '@type' => 'InsuranceCompany',
                        'name' => 'Insurance Company Name',
                    ],
                    'nafCode' => '6202b',
                    'companyRegistrationNumber' => null,
                    'companyCountryCode' => null,
                    'averageDailyRate' => 500,
                    'freelanceCurrency' => 'USD',
                    'employee' => false,
                    'contracts' => [],
                    'locations' => [
                        [
                            '@type' => 'UserMobility',
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
                    'formStep' => 'job_search_preferences',
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideWithValidDataCases
     */
    public function testNotLogged(array $payload): void
    {
        $client = static::createFreeWorkClient();

        $client->request('PATCH', '/users/1/job_search_preferences', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(401);

        $client->request('PATCH', '/users/2/job_search_preferences', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    /**
     * @dataProvider provideWithValidDataCases
     */
    public function testLoggedAsUser(array $payload): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('PATCH', '/users/2/job_search_preferences', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    /**
     * @dataProvider provideWithValidDataCases
     */
    public function testLoggedAsAdmin(array $payload): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('PATCH', '/users/1/job_search_preferences', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    /**
     * @dataProvider provideWithValidDataCases
     */
    public function testWithValidDataOnItsOwnEntityAndLoggedAsUser(array $payload, array $expected): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient(); // id 1
        $client->request('PATCH', '/users/1/job_search_preferences', [
            'json' => $payload,
        ]);

        self::assertResponseIsSuccessful();
        self::assertJsonContains($expected);
    }

    public static function provideWithErrorOnItsOwnEntityAndLoggedAsUserCases(): iterable
    {
        return [
            [
                [
                    'freelance' => false,
                    'employee' => false,
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => '',
                            'message' => 'Vous devez au moins sélectionner Salarié ou Freelance.',
                        ],
                    ],
                ],
            ],
            [
                [
                    'jobs' => [],
                    'fulltimeTeleworking' => true,
                    'employmentTime' => ByteString::fromRandom(256),
                    'freelance' => true,
                    'freelanceLegalStatus' => ByteString::fromRandom(256),
                    'averageDailyRate' => 0,
                    'employee' => true,
                    'grossAnnualSalary' => 1000,
                    'contracts' => [],
                    'locationKeys' => [
                        ByteString::fromRandom(256),
                        ByteString::fromRandom(256),
                    ],
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'contracts',
                            'message' => 'Vous devez sélectionner au moins 1 choix.',
                        ],
                        [
                            'propertyPath' => 'freelanceLegalStatus',
                            'message' => 'Cette valeur doit être l\'un des choix proposés.',
                        ],
                        [
                            'propertyPath' => 'employmentTime',
                            'message' => 'Cette valeur doit être l\'un des choix proposés.',
                        ],
                        [
                            'propertyPath' => 'grossAnnualSalary',
                            'message' => 'Cette valeur doit être entre 7000 et 10000000. C\'est un salaire annuel.',
                        ],
                        [
                            'propertyPath' => 'averageDailyRate',
                            'message' => 'Cette valeur doit être strictement positive.',
                        ],
                        [
                            'propertyPath' => 'locations',
                            'message' => 'Veuillez choisir les éléments obligatoirement dans la liste proposée.',
                        ],
                        [
                            'propertyPath' => 'jobs',
                            'message' => 'Veuillez choisir les éléments obligatoirement dans la liste proposée.',
                        ],
                    ],
                ],
            ],
            [
                [
                    'jobs' => [
                        [
                            'job' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase',
                            'main' => true,
                        ],
                    ],
                    'fulltimeTeleworking' => false,
                    'employmentTime' => 'full_time',
                    'freelance' => true,
                    'insurance' => true,
                    'freelanceLegalStatus' => 'self_employed',
                    'companyCountryCode' => 'FR',
                    'companyRegistrationNumber' => '123456789',
                    'companyRegistrationNumberBeingAttributed' => false,
                    'nafCode' => 'Invalid NAF code',
                    'averageDailyRate' => 500,
                    'employee' => false,
                    'contracts' => [],
                    'locationKeys' => [
                        'fr~auvergne-rhone-alpes~seyssinet-pariset',
                    ],
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'companyRegistrationNumber',
                            'message' => 'Le numéro de Siren 123456789 est invalide.',
                        ],
                        [
                            'propertyPath' => 'insuranceCompany',
                            'message' => 'Veuillez renseigner le nom de votre assurance.',
                        ],
                        [
                            'propertyPath' => 'insuranceNumber',
                            'message' => 'Veuillez renseigner votre numéro de police d\'assurance.',
                        ],
                        [
                            'propertyPath' => 'insuranceExpiredAt',
                            'message' => 'Veuillez renseigner la date d\'échéance de contrat.',
                        ],
                        [
                            'propertyPath' => 'nafCode',
                            'message' => 'Cette valeur doit être l\'un des choix proposés.',
                        ],
                    ],
                ],
            ],
            [
                [
                    'jobs' => [
                        [
                            'job' => '/jobs/administrateur-de-base-de-donnee-oracle-sybase',
                            'main' => true,
                        ],
                        [
                            'job' => '/jobs/administrateur-systeme-linux',
                            'main' => true,
                        ],
                        [
                            'job' => '/jobs/administrateur-applicatif-erp-crm-sirh',
                            'main' => true,
                        ],
                        [
                            'job' => '/jobs/administrateur-oracle',
                            'main' => true,
                        ],
                    ],
                    'grossAnnualSalary' => null,
                    'employee' => true,
                    'contracts' => [
                        'permanent',
                        'apprenticeship',
                        'fixed-term',
                    ],
                    'locationKeys' => [
                        'fr~auvergne-rhone-alpes~seyssinet-pariset',
                    ],
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'grossAnnualSalary',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'jobs',
                            'message' => 'Vous pouvez mettre en avant jusqu\'à 3 métiers recherchés au maximum.',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideWithErrorOnItsOwnEntityAndLoggedAsUserCases
     */
    public function testWithErrorOnItsOwnEntityAndLoggedAsUser(array $payload, array $expected): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient(); // id 1
        $client->request('PATCH', '/users/1/job_search_preferences', [
            'json' => $payload,
        ]);

        self::assertJsonContains($expected);
    }
}
