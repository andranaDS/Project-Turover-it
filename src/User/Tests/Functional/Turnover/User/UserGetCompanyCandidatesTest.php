<?php

namespace App\User\Tests\Functional\Turnover\User;

use App\Tests\Functional\ApiTestCase;

class UserGetCompanyCandidatesTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/companies/company-1/candidates');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedNotOnMe(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/companies/company-2/candidates');

        self::assertResponseStatusCodeSame(200);
    }

    public function testLoggedOnMe(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/companies/company-1/candidates');

        self::assertResponseStatusCodeSame(200);
        self::assertJsonContains([
            '@context' => '/contexts/User',
            '@id' => '/legacy/users',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/users/42',
                    '@type' => 'User',
                    'id' => 42,
                    'firstName' => 'FirstName Intercontract 1 Company 1',
                    'lastName' => 'LastName Intercontract 1 Company 1',
                    'profileJobTitle' => 'Profile Intercontract 1 Company 1',
                    'experienceYear' => '1-2_years',
                    'availability' => 'within_1_month',
                    'freelanceCurrency' => null,
                    'averageDailyRate' => 500,
                    'birthdate' => null,
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
                    'locations' => [
                        [
                            '@type' => 'UserMobility',
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
                        [
                            '@type' => 'UserMobility',
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
                                'key' => 'fr~ile-de-france~~',
                                'label' => 'Île-de-France, France',
                                'shortLabel' => 'Île-de-France',
                            ],
                        ],
                    ],
                    'formation' => [
                        'diplomaLevel' => 5,
                    ],
                    'skills' => [
                        [
                            '@type' => 'UserSkill',
                            'skill' => [
                                '@id' => '/skills/1',
                                '@type' => 'Skill',
                                'id' => 1,
                                'name' => 'php',
                            ],
                        ],
                        [
                            '@type' => 'UserSkill',
                            'skill' => [
                                '@id' => '/skills/2',
                                '@type' => 'Skill',
                                'id' => 2,
                                'name' => 'java',
                            ],
                        ],
                    ],
                    'languages' => [
                        [
                            'language' => 'fr',
                            'languageLevel' => 'native_or_bilingual',
                        ],
                        [
                            'language' => 'en',
                            'languageLevel' => 'limited_professional_skills',
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
                            '@id' => '/soft_skills/3',
                            '@type' => 'SoftSkill',
                            'id' => 3,
                            'name' => 'SoftSkill 3',
                            'slug' => 'softskill-3',
                        ],
                    ],
                ],
            ],
            'hydra:totalItems' => 1,
        ]);
    }
}
