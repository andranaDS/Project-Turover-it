<?php

namespace App\Recruiter\Tests\Functional\Turnover\Recruiter;

use App\Tests\Functional\ApiTestCase;

class PatchItemTest extends ApiTestCase
{
    public static function provideValidCases(): iterable
    {
        return [
            ['/recruiters/me'],
            ['/recruiters/1'],
        ];
    }

    /**
     * @dataProvider provideValidCases
     */
    public function testValidCases(string $path): void
    {
        $client = static::createTurnoverAuthenticatedClient();

        $client->request('PATCH', $path, [
            'json' => [
                'firstName' => 'W',
                'lastName' => 'Heisenberg',
                'phoneNumber' => '+33687654321',
                'gender' => 'female',
            ],
        ]);

        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Recruiter',
            '@id' => '/recruiters/1',
            '@type' => 'Recruiter',
            'id' => 1,
            'email' => 'walter.white@breaking-bad.com',
            'username' => 'walter.white',
            'gender' => 'female',
            'firstName' => 'W',
            'lastName' => 'Heisenberg',
            'phoneNumber' => '+33687654321',
            'enabled' => true,
            'passwordUpdateRequired' => true,
            'company' => [
                '@id' => '/companies/company-1',
                '@type' => 'Company',
                'id' => 1,
                'name' => 'Company 1',
                'slug' => 'company-1',
                'businessActivity' => '/company_business_activities/1',
                'billingAddress' => [
                    '@type' => 'Location',
                    'countryCode' => 'FR',
                ],
                'registrationNumber' => null,
            ],
            'site' => null,
            'main' => true,
            'job' => 'CTO',
            'termsOfService' => true,
            'notification' => [
                'newApplicationEmail' => true,
                'newApplicationNotification' => true,
                'endBroadcastJobPostingEmail' => true,
                'endBroadcastJobPostingNotification' => true,
                'dailyResumeEmail' => true,
                'dailyJobPostingEmail' => true,
                'jobPostingPublishATSEmail' => true,
                'jobPostingPublishATSNotification' => true,
                'newsletterEmail' => true,
                'subscriptionEndEmail' => true,
                'subscriptionEndNotification' => true,
                'invoiceEmail' => true,
                'invoiceNotification' => true,
                'orderEmail' => true,
                'orderNotification' => true,
                'subscriptionEndReminderEmail' => true,
                'subscriptionEndReminderNotification' => true,
            ],
        ]);
    }

    public function testNotLoggedCases(): void
    {
        $client = static::createTurnoverClient();
        $client->request('PATCH', '/recruiters/1', [
            'json' => [
                'firstName' => 'W',
                'lastName' => 'Heisenberg',
                'phoneNumber' => '+33687654321',
                'gender' => 'female',
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedOnOtherRecruiter(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('PATCH', '/recruiters/2', [
            'json' => [
                'firstName' => 'W',
                'lastName' => 'Heisenberg',
                'phoneNumber' => '+33687654321',
                'gender' => 'female',
            ],
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    public static function provideInvalidCases(): iterable
    {
        return [
            [
                [
                    'firstName' => '',
                    'lastName' => '',
                    'phoneNumber' => '',
                    'gender' => '',
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'gender',
                            'message' => "Cette valeur doit être l'un des choix proposés.",
                        ],
                        [
                            'propertyPath' => 'firstName',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'lastName',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'phoneNumber',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                    ],
                ],
            ],
            [
                [
                    'firstName' => 'W',
                    'lastName' => '',
                    'phoneNumber' => 'NAN',
                    'gender' => 'trans',
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'gender',
                            'message' => "Cette valeur doit être l'un des choix proposés.",
                        ],
                        [
                            'propertyPath' => 'lastName',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'phoneNumber',
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
        $client->request('PATCH', '/recruiters/me', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains($expected);
    }
}
