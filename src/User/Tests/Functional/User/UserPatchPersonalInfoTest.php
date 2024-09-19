<?php

namespace App\User\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;
use Symfony\Component\String\ByteString;

class UserPatchPersonalInfoTest extends ApiTestCase
{
    public static function provideWithValidDataCases(): iterable
    {
        return [
            [
                [
                    'phone' => '+33612345678',
                    'birthdate' => '1980-01-01T00:00:00+01:00',
                    'profileJobTitle' => 'Profile Job Title',
                    'experienceYear' => 'less_than_1_year',
                    'availability' => 'immediate',
                    'locationKey' => 'fr~nouvelle-aquitaine~~bordeaux',
                    'drivingLicense' => true,
                    'anonymous' => true,
                    'blacklistedCompanies' => [
                        [
                            'company' => '/companies/company-1',
                        ],
                    ],
                ],
                [
                    '@context' => '/contexts/User',
                    '@type' => 'User',
                    'phone' => '+33612345678',
                    'birthdate' => '1980-01-01T00:00:00+01:00',
                    'profileJobTitle' => 'Profile Job Title',
                    'experienceYear' => 'less_than_1_year',
                    'availability' => 'immediate',
                    'location' => [
                        'street' => null,
                        'locality' => 'Bordeaux',
                        'postalCode' => '33000',
                        'adminLevel1' => 'Nouvelle-Aquitaine',
                        'adminLevel2' => null,
                        'country' => 'France',
                        'countryCode' => 'FR',
                        'latitude' => '44.8412250',
                        'longitude' => '-0.5800364',
                        'key' => 'fr~nouvelle-aquitaine~~bordeaux',
                        'label' => 'Bordeaux, Nouvelle-Aquitaine',
                        'shortLabel' => 'Bordeaux (33)',
                    ],
                    'drivingLicense' => true,
                    'anonymous' => true,
                    'formStep' => 'personal_info',
                    'blacklistedCompanies' => [
                        [
                            'company' => [
                                '@id' => '/companies/company-1',
                                '@type' => 'Company',
                                'name' => 'Company 1',
                                'slug' => 'company-1',
                            ],
                        ],
                    ],
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

        $client->request('PATCH', '/users/1/personal_info', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(401);

        $client->request('PATCH', '/users/2/personal_info', [
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
        $client->request('PATCH', '/users/2/personal_info', [
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
        $client->request('PATCH', '/users/1/personal_info', [
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

        $client->request('PATCH', '/users/1/personal_info', [
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
                    'phone' => '+336123456789',
                    'profileJobTitle' => ByteString::fromRandom(256),
                    'experienceYear' => ByteString::fromRandom(256),
                    'availability' => ByteString::fromRandom(256),
                    'locationKey' => ByteString::fromRandom(256),
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'phone',
                            'message' => 'Ce numéro de téléphone n\'est pas valide.',
                        ],
                        [
                            'propertyPath' => 'profileJobTitle',
                            'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 100 caractères.',
                        ],
                        [
                            'propertyPath' => 'experienceYear',
                            'message' => 'Cette valeur doit être l\'un des choix proposés.',
                        ],
                        [
                            'propertyPath' => 'availability',
                            'message' => 'Cette valeur doit être l\'un des choix proposés.',
                        ],
                        [
                            'propertyPath' => 'location',
                            'message' => 'Veuillez choisir une ville obligatoirement dans la liste proposée.',
                        ],
                    ],
                ],
            ],
            [
                [
                    'phone' => '',
                    'profileJobTitle' => '',
                    'experienceYear' => '',
                    'availability' => '',
                    'locationKey' => '',
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'phone',
                            'message' => 'Ce numéro de téléphone n\'est pas valide.',
                        ],
                        [
                            'propertyPath' => 'profileJobTitle',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'experienceYear',
                            'message' => 'Cette valeur doit être l\'un des choix proposés.',
                        ],
                        [
                            'propertyPath' => 'experienceYear',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'availability',
                            'message' => 'Cette valeur doit être l\'un des choix proposés.',
                        ],
                        [
                            'propertyPath' => 'location',
                            'message' => 'Veuillez choisir une ville obligatoirement dans la liste proposée.',
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
        $client->request('PATCH', '/users/1/personal_info', [
            'json' => $payload,
        ]);

        self::assertJsonContains($expected);
    }
}
