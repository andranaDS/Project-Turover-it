<?php

namespace App\Recruiter\Tests\Functional\Turnover\Recruiter;

use App\Tests\Functional\ApiTestCase;

class PatchItemSecondaryTest extends ApiTestCase
{
    public function testNotLoggedCases(): void
    {
        $client = static::createTurnoverClient();
        $client->request('PATCH', '/companies/mine/recruiters/2', [
            'json' => [
                'firstName' => 'J',
                'lastName' => 'Pinkman',
                'site' => '/sites/2',
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedOnOtherRecruiterOnOtherCompany(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('PATCH', '/companies/mine/recruiters/4', [
            'json' => [
                'firstName' => 'Ed',
                'lastName' => 'Stark',
                'site' => '/sites/2',
            ],
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    public static function provideValidCases(): iterable
    {
        return [
            'valid' => [
                [
                    'firstName' => 'J',
                    'lastName' => 'Pinkman',
                    'site' => '/sites/2',
                ],
                [
                    '@context' => '/contexts/Recruiter',
                    '@id' => '/recruiters/2',
                    'id' => 2,
                    'email' => 'jesse.pinkman@breaking-bad.com',
                    'firstName' => 'J',
                    'lastName' => 'Pinkman',
                    'company' => [
                        '@id' => '/companies/company-1',
                        '@type' => 'Company',
                        'id' => 1,
                        'name' => 'Company 1',
                        'slug' => 'company-1',
                        'businessActivity' => '/company_business_activities/1',
                    ],
                    'site' => [
                        '@id' => '/sites/2',
                        '@type' => 'Site',
                        'id' => 2,
                        'name' => 'Site 2  - Company 1',
                        'slug' => 'site-2-company-1',
                    ],
                    'main' => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideValidCases
     */
    public function testValidCases(array $payload, array $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient();

        $client->request('PATCH', '/companies/mine/recruiters/2', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains($expected);
    }

    public static function provideInvalidCases(): iterable
    {
        return [
            [
                [
                    'firstName' => '',
                    'lastName' => '',
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'firstName',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'lastName',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                    ],
                ],
            ],
            [
                [
                    'firstName' => 'W',
                    'lastName' => '',
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'lastName',
                            'message' => 'Cette valeur ne doit pas être vide.',
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
        $client->request('PATCH', '/companies/mine/recruiters/2', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains($expected);
    }
}
