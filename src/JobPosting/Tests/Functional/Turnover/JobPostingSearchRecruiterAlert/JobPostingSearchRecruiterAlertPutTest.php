<?php

namespace App\JobPosting\Tests\Functional\Turnover\JobPostingSearchRecruiterAlert;

use App\Tests\Functional\ApiTestCase;
use Symfony\Component\String\ByteString;

class JobPostingSearchRecruiterAlertPutTest extends ApiTestCase
{
    public static function provideValidCases(): iterable
    {
        return [
            'complete_alert' => [
                [
                    'title' => 'JobPostingSearchRecruiterAlert 3 - User 1 title',
                    'keywords' => 'JobPostingSearchRecruiterAlert 3 - User 1',
                    'remoteMode' => ['full', 'partial'],
                    'publishedSince' => 'from_1_to_7_days',
                    'minDailySalary' => 300,
                    'maxDailySalary' => 450,
                    'currency' => 'EUR',
                    'minDuration' => 30,
                    'maxDuration' => 360,
                    'intercontractOnly' => false,
                    'startsAt' => '2023-01-01 08:00:00',
                    'businessActivity' => '/company_business_activities/2',
                    'locationKeys' => [
                        'fr~nouvelle-aquitaine~~bordeaux',
                        'fr~ile-de-france~~paris',
                    ],
                    'active' => true,
                ],
                [
                    '@context' => '/contexts/JobPostingSearchRecruiterAlert',
                    '@type' => 'JobPostingSearchRecruiterAlert',
                    'title' => 'JobPostingSearchRecruiterAlert 3 - User 1 title',
                    'keywords' => 'JobPostingSearchRecruiterAlert 3 - User 1',
                    'remoteMode' => ['full', 'partial'],
                    'publishedSince' => 'from_1_to_7_days',
                    'minDailySalary' => 300,
                    'maxDailySalary' => 450,
                    'minDuration' => 30,
                    'maxDuration' => 360,
                    'currency' => 'EUR',
                    'intercontractOnly' => false,
                    'startsAt' => '2023-01-01T08:00:00+01:00',
                    'businessActivity' => [
                        '@id' => '/company_business_activities/2',
                        '@type' => 'CompanyBusinessActivity',
                        'id' => 2,
                        'name' => 'Business activity 2',
                        'slug' => 'business-activity-2',
                    ],
                    'locations' => [
                        [
                            '@type' => 'JobPostingSearchRecruiterAlertLocation',
                            'location' => [
                                '@type' => 'Location',
                                'locality' => 'Bordeaux',
                                'postalCode' => '33000',
                                'adminLevel1' => 'Nouvelle-Aquitaine',
                                'adminLevel2' => null,
                                'country' => 'France',
                                'countryCode' => 'FR',
                                'latitude' => '44.841225',
                                'longitude' => '-0.5800364',
                                'key' => 'fr~nouvelle-aquitaine~~bordeaux',
                                'label' => 'Bordeaux, Nouvelle-Aquitaine',
                                'shortLabel' => 'Bordeaux (33)',
                            ],
                        ],
                        [
                            '@type' => 'JobPostingSearchRecruiterAlertLocation',
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
                                'key' => 'fr~ile-de-france~~paris',
                                'label' => 'Paris, Île-de-France',
                                'shortLabel' => 'Paris (75)',
                            ],
                        ],
                    ],
                    'active' => true,
                ],
            ],
            'empty_alert' => [
                [
                    'title' => 'JobPostingSearchRecruiterAlert 4 - User 1 title',
                    'keywords' => 'JobPostingSearchRecruiterAlert 4 - User 1 Empty',
                    'remoteMode' => null,
                    'publishedSince' => null,
                    'minDailySalary' => null,
                    'maxDailySalary' => null,
                    'currency' => null,
                    'minDuration' => null,
                    'maxDuration' => null,
                    'intercontractOnly' => false,
                    'startsAt' => null,
                    'businessActivity' => null,
                    'locationKeys' => null,
                    'active' => true,
                ],
                [
                    '@context' => '/contexts/JobPostingSearchRecruiterAlert',
                    '@type' => 'JobPostingSearchRecruiterAlert',
                    'title' => 'JobPostingSearchRecruiterAlert 4 - User 1 title',
                    'keywords' => 'JobPostingSearchRecruiterAlert 4 - User 1 Empty',
                    'remoteMode' => null,
                    'publishedSince' => null,
                    'minDailySalary' => null,
                    'maxDailySalary' => null,
                    'currency' => null,
                    'minDuration' => null,
                    'maxDuration' => null,
                    'intercontractOnly' => false,
                    'startsAt' => null,
                    'businessActivity' => null,
                    'locations' => [],
                    'active' => true,
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideValidCases
     */
    public function testNotLogged(array $payload): void
    {
        $client = static::createTurnoverClient();
        $client->request('PUT', '/job_posting_search_recruiter_alerts/1', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    /**
     * @dataProvider provideValidCases
     */
    public function testLoggedAsNotOwner(array $payload): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('PUT', '/job_posting_search_recruiter_alerts/3', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    /**
     * @dataProvider provideValidCases
     */
    public function testValid(array $payload, array $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('PUT', '/job_posting_search_recruiter_alerts/1', [
            'json' => $payload,
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains($expected);
    }

    public static function provideInvalidCases(): iterable
    {
        return [
            'empty_title' => [
                [
                    'title' => '',
                    'keywords' => null,
                    'remoteMode' => ['full', 'partial'],
                    'publishedSince' => 'from_1_to_7_days',
                    'minDailySalary' => 300,
                    'maxDailySalary' => 450,
                    'currency' => 'EUR',
                    'minDuration' => 30,
                    'maxDuration' => 360,
                    'intercontractOnly' => false,
                    'startsAt' => null,
                    'businessActivity' => '/company_business_activities/2',
                    'locationKeys' => [
                        'fr~nouvelle-aquitaine~~bordeaux',
                        'fr~ile-de-france~~paris',
                    ],
                    'active' => true,
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'title',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                    ],
                ],
            ],
            'length' => [
                [
                    'title' => ByteString::fromRandom(256),
                    'keywords' => ByteString::fromRandom(256),
                    'remoteMode' => ['full', 'partial'],
                    'publishedSince' => 'from_1_to_7_days',
                    'minDailySalary' => 300,
                    'maxDailySalary' => 450,
                    'currency' => 'EUR',
                    'minDuration' => 30,
                    'maxDuration' => 360,
                    'intercontractOnly' => false,
                    'startsAt' => null,
                    'businessActivity' => '/company_business_activities/2',
                    'locationKeys' => [
                        'fr~nouvelle-aquitaine~~bordeaux',
                        'fr~ile-de-france~~paris',
                    ],
                    'active' => true,
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'title',
                            'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 255 caractères.',
                        ],
                        [
                            'propertyPath' => 'keywords',
                            'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 255 caractères.',
                        ],
                    ],
                ],
            ],
            'invalid_enum' => [
                [
                    'title' => 'JobPostingSearchRecruiterAlert 5 - User 1 Invalid Enum',
                    'keywords' => 'JobPostingSearchRecruiterAlert 5 - User 1 Invalid Enum',
                    'remoteMode' => [ByteString::fromRandom(255)],
                    'publishedSince' => ByteString::fromRandom(255),
                    'minDailySalary' => 300,
                    'maxDailySalary' => 450,
                    'currency' => 'EUR',
                    'minDuration' => 30,
                    'maxDuration' => 360,
                    'intercontractOnly' => false,
                    'startsAt' => null,
                    'businessActivity' => '/company_business_activities/2',
                    'locationKeys' => [
                        'fr~nouvelle-aquitaine~~bordeaux',
                        'fr~ile-de-france~~paris',
                    ],
                    'active' => true,
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'remoteMode',
                            'message' => 'Une ou plusieurs des valeurs soumises sont invalides.',
                        ],
                        [
                            'propertyPath' => 'publishedSince',
                            'message' => 'Cette valeur doit être l\'un des choix proposés.',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideInvalidCases
     */
    public function testInvalidCases(array $payload, array $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('POST', '/job_posting_search_recruiter_alerts', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains($expected);
    }
}
