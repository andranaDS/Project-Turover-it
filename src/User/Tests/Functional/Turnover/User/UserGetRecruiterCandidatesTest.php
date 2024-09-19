<?php

namespace App\User\Tests\Functional\Turnover\User;

use App\Tests\Functional\ApiTestCase;

class UserGetRecruiterCandidatesTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/recruiters/me/candidates');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLogged(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/recruiters/me/candidates');

        self::assertResponseStatusCodeSame(200);
        self::assertJsonContains([
            '@context' => '/contexts/User',
            '@id' => '/legacy/users',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/users/42',
                    '@type' => 'User',
                    'profileJobTitle' => 'Profile Intercontract 1 Company 1',
                    'experienceYear' => '1-2_years',
                    'availability' => 'within_1_month',
                    'freelanceCurrency' => null,
                    'averageDailyRate' => 500,
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
                            ],
                        ],
                    ],
                    'formation' => [
                        'diplomaLevel' => 5,
                    ],
                    'skills' => [
                        [
                            '@type' => 'UserSkill',
                            'skill' => '/skills/1',
                        ],
                        [
                            '@type' => 'UserSkill',
                            'skill' => '/skills/2',
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
                        '/soft_skills/1',
                        '/soft_skills/3',
                    ],
                ],
            ],
            'hydra:totalItems' => 2,
        ]);
    }
}
