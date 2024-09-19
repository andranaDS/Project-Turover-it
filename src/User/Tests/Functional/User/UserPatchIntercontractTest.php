<?php

namespace App\User\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;

class UserPatchIntercontractTest extends ApiTestCase
{
    public static function provideWithValidDataCases(): iterable
    {
        return [
            [
                [
                    'availability' => 'immediate',
                    'visible' => false,
                    'profileJobTitle' => 'Titre CV patch',
                    'reference' => 'Référence patch',
                    'jobs' => [
                        [
                            'job' => '/jobs/administrateur-systeme-linux',
                        ],
                    ],
                    'formation' => [
                        'diplomaLevel' => 3,
                    ],
                    'locationKeys' => [
                        'fr~ile-de-france~~paris',
                    ],
                    'experienceYear' => '1-2_years',
                    'diplomaLevel' => 1,
                    'averageDailyRate' => 1000,
                    'grossAnnualSalary' => 10000,
                    'freelanceCurrency' => 'USD',
                    'skills' => [
                        [
                            'skill' => '/skills/3',
                        ],
                    ],
                    'softSkills' => [
                        '/soft_skills/3',
                    ],
                    'introduceYourself' => 'Description du profil Patch',
                    'contact' => 'Coordonnées de la personne à contacter pour ce profil Patch',
                    'languages' => [
                        [
                            'language' => 'fr',
                            'languageLevel' => 'limited_professional_skills',
                        ],
                    ],
                ],
                [
                    '@context' => '/contexts/User',
                    '@id' => '/users/42',
                    '@type' => 'User',
                    'profileJobTitle' => 'Titre CV patch',
                    'experienceYear' => '1-2_years',
                    'visible' => false,
                    'availability' => 'immediate',
                    'freelanceCurrency' => 'USD',
                    'introduceYourself' => 'Description du profil Patch',
                    'grossAnnualSalary' => 10000,
                    'averageDailyRate' => 1000,
                    'locations' => [
                        [
                            'location' => [
                                '@type' => 'Location',
                                'street' => null,
                                'locality' => 'Paris',
                                'postalCode' => '75000',
                                'adminLevel1' => 'Île-de-France',
                                'adminLevel2' => null,
                                'country' => 'France',
                                'countryCode' => 'FR',
                                'latitude' => '48.8566969',
                                'longitude' => '2.3514616',
                            ],
                        ],
                    ],
                    'formation' => [
                        '@type' => 'UserFormation',
                        'diplomaLevel' => 1,
                    ],
                    'skills' => [
                        [
                            '@type' => 'UserSkill',
                            'skill' => '/skills/3',
                        ],
                    ],
                    'languages' => [
                        [
                            'language' => 'fr',
                            'languageLevel' => 'limited_professional_skills',
                        ],
                    ],
                    'jobs' => [
                        [
                            '@type' => 'UserJob',
                            'job' => '/jobs/administrateur-systeme-linux',
                        ],
                    ],
                    'softSkills' => [
                        '/soft_skills/3',
                    ],
                    'reference' => 'Référence patch',
                    'contact' => 'Coordonnées de la personne à contacter pour ce profil Patch',
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideWithValidDataCases
     */
    public function testLoggedUserFreeWork(array $payload): void
    {
        $client = static::createFreeWorkAuthenticatedClient();

        $client->request('POST', '/users/42', [
            'headers' => [
                'Content-Type' => 'multipart/form-data',
            ],
            'extra' => [
                'parameters' => $payload,
            ],
        ]);

        self::assertResponseStatusCodeSame(405);
    }

    /**
     * @dataProvider provideWithValidDataCases
     */
    public function testLoggedRecruiterNotCreated(array $payload): void
    {
        $client = static::createTurnoverAuthenticatedClient('jesse.pinkman@breaking-bad.com');
        $client->request('POST', '/users/42', [
            'headers' => [
                'Content-Type' => 'multipart/form-data',
            ],
            'extra' => [
                'parameters' => $payload,
            ],
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    /**
     * @dataProvider provideWithValidDataCases
     */
    public function testWithValidDataOnItsOwnEntityAndLoggedAsRecruiter(array $payload, array $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient(); // id 1

        $client->request('POST', '/users/42', [
            'headers' => [
                'Content-Type' => 'multipart/form-data',
            ],
            'extra' => [
                'parameters' => $payload,
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertJsonContains($expected);
    }
}
