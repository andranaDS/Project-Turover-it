<?php

namespace App\Company\Tests\Functional\Turnover\Company;

use App\Tests\Functional\ApiTestCase;
use Symfony\Component\String\ByteString;

class CompanyPatchAccountTest extends ApiTestCase
{
    public static function provideValidCases(): iterable
    {
        return [
            ['/companies/company-1/account'],
            ['/companies/mine/account'],
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
                'legalName' => 'Company 1 Legal Name Patch',
                'businessActivity' => '/company_business_activities/3',
                'billingAddress' => [
                    'street' => '10 Rue de Rennes',
                    'locality' => 'Paris',
                    'postalCode' => '75006',
                    'countryCode' => 'FR',
                    'additionalData' => '0609B',
                ],
                'registrationNumber' => '75029882000028',
                'intracommunityVat' => 'FR26750298820',
            ],
        ]);

        self::assertResponseStatusCodeSame(200);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Company',
            '@id' => '/companies/company-1',
            '@type' => 'Company',
            'legalName' => 'Company 1 Legal Name Patch',
            'businessActivity' => [
                '@type' => 'CompanyBusinessActivity',
                'id' => 3,
                'name' => 'Business activity 3',
            ],
            'billingAddress' => [
                '@type' => 'Location',
                'street' => '10 Rue de Rennes',
                'locality' => 'Paris',
                'postalCode' => '75006',
                'country' => 'France',
                'countryCode' => 'FR',
                'additionalData' => '0609B',
            ],
            'registrationNumber' => '75029882000028',
            'intracommunityVat' => 'FR26750298820',
        ]);
    }

    /**
     * @dataProvider provideValidCases
     */
    public function testNotLoggedCases(string $path): void
    {
        $client = static::createTurnoverClient();
        $client->request('PATCH', $path, [
            'json' => [
                'legalName' => 'Company 1 Legal Name Patch',
                'businessActivity' => '/company_business_activities/3',
                'billingAddress' => [
                    'street' => '10 Rue de Rennes',
                    'locality' => 'Paris',
                    'postalCode' => '75006',
                    'countryCode' => 'FR',
                    'additionalData' => '0609B',
                ],
                'registrationNumber' => '75029882000028',
                'intracommunityVat' => 'FR26750298820',
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    /**
     * @dataProvider provideValidCases
     */
    public function testLoggedOnWrongCompany(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('PATCH', '/companies/company-2/account', [
            'json' => [
                'legalName' => 'Company 1 Legal Name Patch',
                'businessActivity' => '/company_business_activities/3',
                'billingAddress' => [
                    'street' => '10 Rue de Rennes',
                    'locality' => 'Paris',
                    'postalCode' => '75006',
                    'countryCode' => 'FR',
                    'additionalData' => '0609B',
                ],
                'registrationNumber' => '75029882000028',
                'intracommunityVat' => 'FR26750298820',
            ],
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    public static function provideInvalidCases(): iterable
    {
        return [
            'empty' => [
                [
                    'legalName' => '',
                    'businessActivity' => null,
                    'billingAddress' => [
                        'street' => '',
                        'locality' => '',
                        'postalCode' => '',
                        'countryCode' => '',
                        'additionalData' => '',
                    ],
                    'registrationNumber' => '',
                    'intracommunityVat' => '',
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'businessActivity',
                            'message' => 'Cette valeur ne doit pas être nulle.',
                        ],
                        [
                            'propertyPath' => 'legalName',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'intracommunityVat',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'billingAddress.street',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'billingAddress.locality',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'billingAddress.postalCode',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                        [
                            'propertyPath' => 'billingAddress.countryCode',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                    ],
                ],
            ],
            'length' => [
                [
                    'legalName' => ByteString::fromRandom(256),
                    'businessActivity' => '/company_business_activities/1',
                    'billingAddress' => [
                        'street' => ByteString::fromRandom(256),
                        'locality' => ByteString::fromRandom(256),
                        'postalCode' => ByteString::fromRandom(256),
                        'countryCode' => ByteString::fromRandom(256),
                        'additionalData' => ByteString::fromRandom(256),
                    ],
                    'registrationNumber' => ByteString::fromRandom(256),
                    'intracommunityVat' => ByteString::fromRandom(256),
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'registrationNumber',
                            'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 255 caractères.',
                        ],
                        [
                            'propertyPath' => 'legalName',
                            'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 255 caractères.',
                        ],
                        [
                            'propertyPath' => 'intracommunityVat',
                            'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 255 caractères.',
                        ],
                        [
                            'propertyPath' => 'billingAddress.street',
                            'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 255 caractères.',
                        ],
                        [
                            'propertyPath' => 'billingAddress.locality',
                            'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 255 caractères.',
                        ],
                        [
                            'propertyPath' => 'billingAddress.postalCode',
                            'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 255 caractères.',
                        ],
                        [
                            'propertyPath' => 'billingAddress.countryCode',
                            'message' => 'Cette valeur doit être l\'un des choix proposés.',
                        ],
                        [
                            'propertyPath' => 'billingAddress.additionalData',
                            'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 255 caractères.',
                        ],
                    ],
                ],
            ],
            'invalid_registration_number' => [
                [
                    'legalName' => ByteString::fromRandom(255),
                    'businessActivity' => '/company_business_activities/1',
                    'billingAddress' => [
                        'street' => ByteString::fromRandom(255),
                        'locality' => ByteString::fromRandom(255),
                        'postalCode' => ByteString::fromRandom(255),
                        'countryCode' => 'FR',
                        'additionalData' => ByteString::fromRandom(255),
                    ],
                    'registrationNumber' => ByteString::fromRandom(255),
                    'intracommunityVat' => ByteString::fromRandom(255),
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'registrationNumber',
                            'message' => 'Le numéro de Siren doit avoir 14 chiffres.',
                        ],
                    ],
                ],
            ],
            'invalid_intracommunity_vat' => [
                [
                    'legalName' => ByteString::fromRandom(255),
                    'businessActivity' => '/company_business_activities/1',
                    'billingAddress' => [
                        'street' => ByteString::fromRandom(255),
                        'locality' => ByteString::fromRandom(255),
                        'postalCode' => ByteString::fromRandom(255),
                        'countryCode' => 'FR',
                        'additionalData' => ByteString::fromRandom(255),
                    ],
                    'registrationNumber' => '75029882000028',
                    'intracommunityVat' => '123456789',
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'intracommunityVat',
                            'message' => 'Le numéro de TVA intracommunautaire 123456789 est invalide.',
                        ],
                    ],
                ],
            ],
            'registration number not blank' => [
                [
                    'legalName' => ByteString::fromRandom(255),
                    'businessActivity' => '/company_business_activities/1',
                    'billingAddress' => [
                        'street' => ByteString::fromRandom(255),
                        'locality' => ByteString::fromRandom(255),
                        'postalCode' => ByteString::fromRandom(255),
                        'countryCode' => 'FR',
                        'additionalData' => ByteString::fromRandom(255),
                    ],
                    'registrationNumber' => null,
                    'intracommunityVat' => null,
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'registrationNumber',
                            'message' => 'Cette valeur ne doit pas être vide.',
                        ],
                    ],
                ],
            ],
            'intra community vat not blank' => [
                [
                    'legalName' => ByteString::fromRandom(255),
                    'businessActivity' => '/company_business_activities/1',
                    'billingAddress' => [
                        'street' => ByteString::fromRandom(255),
                        'locality' => ByteString::fromRandom(255),
                        'postalCode' => ByteString::fromRandom(255),
                        'countryCode' => 'EN',
                        'additionalData' => ByteString::fromRandom(255),
                    ],
                    'registrationNumber' => null,
                    'intracommunityVat' => null,
                ],
                [
                    '@context' => '/contexts/ConstraintViolationList',
                    '@type' => 'ConstraintViolationList',
                    'hydra:title' => 'An error occurred',
                    'violations' => [
                        [
                            'propertyPath' => 'intracommunityVat',
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
        $client->request('PATCH', '/companies/mine/account', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains($expected);
    }
}
