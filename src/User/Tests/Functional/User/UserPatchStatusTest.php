<?php

namespace App\User\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;

class UserPatchStatusTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();

        $client->request('PATCH', '/users/1/status', [
            'json' => [
                'availability' => 'immediate',
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedOnAnotherEntity(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient(); // id 1

        $client->request('PATCH', 'users/2/status', [
            'json' => [
                'availability' => 'immediate',
            ],
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedOnItsOwnEntity(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient(); // id 1

        $client->request('PATCH', '/users/1/status', [
            'json' => [
                'availability' => 'immediate',
            ],
        ]);

        self::assertResponseIsSuccessful();
    }

    public static function provideWithInvalidDataCases(): iterable
    {
        yield [
            [
                'visible' => null,
            ],
            [
                [
                    'propertyPath' => 'visible',
                    'message' => 'Cette valeur ne doit pas être nulle.',
                ],
            ],
        ];

        yield [
            [
                'availability' => null,
            ],
            [
                [
                    'propertyPath' => 'availability',
                    'message' => 'Cette valeur ne doit pas être nulle.',
                ],
            ],
        ];

        yield [
            [
                'availability' => 'wrong-availability',
            ],
            [
                [
                    'propertyPath' => 'availability',
                    'message' => "Cette valeur doit être l'un des choix proposés.",
                ],
            ],
        ];

        yield [
            [
                'availability' => 'wrong-availability',
            ],
            [
                [
                    'propertyPath' => 'availability',
                    'message' => "Cette valeur doit être l'un des choix proposés.",
                ],
            ],
        ];

        yield [
            [
                'availability' => 'date',
                'nextAvailabilityAt' => null,
            ],
            [
                [
                    'propertyPath' => 'nextAvailabilityAt',
                    'message' => 'Cette valeur ne doit pas être vide.',
                ],
            ],
        ];

        yield [
            [
                'availability' => 'date',
                'nextAvailabilityAt' => '2021-01-01',
            ],
            [
                [
                    'propertyPath' => 'nextAvailabilityAt',
                    'message' => 'Vous devez sélectionner une date dans le futur.',
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideWithInvalidDataCases
     */
    public function testWithInvalidData($requestPayload, $responseViolations): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient(); // id 1

        $client->request('PATCH', '/users/1/status', [
            'json' => $requestPayload,
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'violations' => $responseViolations,
        ]);
    }

    public static function provideWithValidDataCases(): iterable
    {
        yield [
            [
                'availability' => 'immediate',
                'visible' => false,
            ],
            [
                'availability' => 'immediate',
                'nextAvailabilityAt' => '+ 0 day',
                'visible' => false,
            ],
        ];

        yield [
            [
                'availability' => 'within_1_month',
                'visible' => false,
            ],
            [
                'availability' => 'within_1_month',
                'nextAvailabilityAt' => '+1 month',
                'visible' => false,
            ],
        ];

        yield [
            [
                'availability' => 'within_2_month',
                'visible' => false,
            ],
            [
                'availability' => 'within_2_month',
                'nextAvailabilityAt' => '+2 months',
                'visible' => false,
            ],
        ];

        yield [
            [
                'availability' => 'within_3_month',
                'visible' => false,
            ],
            [
                'availability' => 'within_3_month',
                'nextAvailabilityAt' => '+3 months',
                'visible' => false,
            ],
        ];

        yield [
            [
                'availability' => 'date',
                'nextAvailabilityAt' => '2030-01-01',
                'visible' => false,
            ],
            [
                'availability' => 'date',
                'nextAvailabilityAt' => '2030-01-01',
                'visible' => false,
            ],
        ];
    }

    /**
     * @dataProvider provideWithValidDataCases
     */
    public function testWithValidData(array $requestPayload, ?array $responseData): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient(); // id 1

        // 1. before
        $response = $client->request('GET', '/users/me');
        self::assertResponseIsSuccessful();

        $data = $response->toArray();
        $oldStatusUpdatedAt = $data['statusUpdatedAt'];
        self::assertNull($data['availability']);
        self::assertTrue($data['visible']);
        self::assertSame('2020-01-01T11:00:00+01:00', $oldStatusUpdatedAt);
        self::assertNull($data['nextAvailabilityAt']);

        // 2. patch
        $client->request('PATCH', '/users/1/status', [
            'json' => $requestPayload,
        ]);
        self::assertResponseIsSuccessful();

        // 3. after
        $response = $client->request('GET', '/users/me');
        self::assertResponseIsSuccessful();

        $data = $response->toArray();
        $newStatusUpdatedAt = $data['statusUpdatedAt'];
        $newNextAvailabilityAt = $data['nextAvailabilityAt'];

        self::assertSame($requestPayload['availability'], $responseData['availability']);
        self::assertSame($requestPayload['visible'], $responseData['visible']);
        self::assertGreaterThan($oldStatusUpdatedAt, $newStatusUpdatedAt);

        $theoreticalNewNextStatusAt = null;
        if (null !== $responseData['nextAvailabilityAt']) {
            $theoreticalNewNextStatusAt = \DateTime::createFromFormat(\DateTimeInterface::RFC3339, $newStatusUpdatedAt)
                ->setTimezone(new \DateTimeZone(date_default_timezone_get()))
                ->modify($responseData['nextAvailabilityAt'])
                ->setTime(0, 0)
                ->format(\DateTimeInterface::RFC3339)
            ;
        }

        self::assertSame($theoreticalNewNextStatusAt, $newNextAvailabilityAt);
    }

    /**
     * @dataProvider provideWithValidDataCases
     */
    public function testProfileCompletedOnItsOwnEntityAndLoggedAsUser(array $requestPayload): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient(); // id 1

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }

        $em = $container->get('doctrine')->getManager();

        // 1 - before
        $user = $em->find(User::class, 1);

        self::assertNotNull($user);
        self::assertFalse($user->getProfileCompleted());

        // 2 - patch all steps

        // 2.1 - personal info step
        $client->request('PATCH', '/users/1/personal_info', [
            'json' => [
                'phone' => '+33612345678',
                'birthdate' => '1980-01-01T00:00:00+01:00',
                'profileJobTitle' => 'Profile Job Title',
                'experienceYear' => 'less_than_1_year',
                'availability' => 'immediate',
                'locationKey' => 'fr~nouvelle-aquitaine~~bordeaux',
                'drivingLicense' => true,
                'anonymous' => true,
            ],
        ]);
        self::assertResponseIsSuccessful();

        // 2.2 - job search preferences
        $client->request('PATCH', '/users/1/job_search_preferences', [
            'json' => [
                'jobs' => [
                    [
                        'job' => '/jobs/administrateur-systeme-linux',
                    ],
                ],
                'fulltimeTeleworking' => false,
                'employmentTime' => 'part_time',
                'freelance' => true,
                'freelanceLegalStatus' => 'umbrella_company',
                'umbrellaCompany' => [
                    'name' => 'Umbrella Company Name',
                ],
                'companyRegistrationNumber' => '123456789',
                'companyCountryCode' => 'BE',
                'averageDailyRate' => 500,
                'employee' => false,
                'locationKeys' => [
                    'fr~hauts-de-france~~le-touquet-paris-plage',
                ],
            ],
        ]);
        self::assertResponseIsSuccessful();

        // 2.3 - skills and languages
        $client->request('PUT', '/users/1/skills_and_languages', [
            'json' => [
                'skills' => [
                    [
                        'skill' => '/skills/1',
                        'main' => true,
                    ],
                    [
                        'skill' => '/skills/2',
                        'main' => true,
                    ],
                    [
                        'skill' => [
                            'name' => 'New Skill',
                        ],
                        'main' => true,
                    ],
                ],
                'languages' => [
                    [
                        'id' => '/user_languages/1',
                        'language' => 'es',
                        'languageLevel' => 'native_or_bilingual',
                    ],
                    [
                        'id' => '/user_languages/2',
                        'language' => 'en',
                        'languageLevel' => null,
                    ],
                ],
                'softSkills' => [
                    '/soft_skills/1',
                    '/soft_skills/2',
                    '/soft_skills/3',
                ],
            ],
        ]);
        self::assertResponseIsSuccessful();

        // 2.4 - education
        $client->request('PATCH', '/users/1/education', [
            'json' => [
                'formation' => [
                    'diplomaTitle' => '',
                    'diplomaLevel' => 0,
                    'school' => '',
                    'diplomaYear' => 0,
                    'beingObtained' => false,
                    'selfTaught' => true,
                ],
            ],
        ]);
        self::assertResponseIsSuccessful();

        // 2.5 - status
        $client->request('PATCH', '/users/1/status', [
            'json' => $requestPayload,
        ]);
        self::assertResponseIsSuccessful();

        // 3 - after all steps
        $user = $em->find(User::class, 1);

        self::assertNotNull($user);
        self::assertTrue($user->getProfileCompleted());
    }
}
