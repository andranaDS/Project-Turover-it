<?php

namespace App\JobPosting\Tests\Functional\FreeWork\Application;

use App\JobPosting\Enum\ApplicationStep;
use App\JobPosting\Enum\Contract;
use App\JobPosting\Enum\RemoteMode;
use App\Tests\Functional\ApiTestCase;
use Symfony\Component\String\ByteString;

class ApplicationPostTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('POST', '/applications', [
            'json' => [
                'jobPosting' => '/job_postings/1',
                'content' => 'Job 1 - Application 3',
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('POST', '/applications', [
            'json' => [
                'jobPosting' => '/job_postings/1',
                'content' => 'Job 1 - Application 3',
                'documents' => [
                    [
                        'document' => '/user_documents/1',
                    ],
                ],
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testWithValidDataOnJobPosting(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('POST', '/applications', [
            'json' => [
                'jobPosting' => '/job_postings/1',
                'content' => 'Job 1 - Application 3',
                'documents' => [
                    [
                        'document' => '/user_documents/1',
                    ],
                    [
                        'document' => '/user_documents/2',
                    ],
                ],
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Application',
            '@type' => 'Application',
            'step' => ApplicationStep::RESUME,
            'state' => [
                'value' => 'in_progress',
                'label' => 'Candidature en cours',
            ],
            'content' => 'Job 1 - Application 3',
            'favoriteAt' => null,
            'seenAt' => null,
            'jobPosting' => [
                'title' => 'Responsable applicatifs Finance (H/F) (CDI)',
                'minAnnualSalary' => 40000,
                'maxAnnualSalary' => 40000,
                'minDailySalary' => null,
                'maxDailySalary' => null,
                'currency' => 'GBP',
                'contracts' => [Contract::PERMANENT],

                'duration' => 24,

                'renewable' => false,
                'remoteMode' => RemoteMode::NONE,
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
                'startsAt' => null,
                'company' => [
                    '@id' => '/companies/company-1',
                    '@type' => 'Company',
                    'id' => 1,
                    'name' => 'Company 1',
                    'slug' => 'company-1',
                    'logo' => [
                        'small' => 'https://api.freework.localhost/media/cache/resolve/company_logo_small/company-1-logo.jpg',
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/company_logo_medium/company-1-logo.jpg',
                    ],
                ],
                'annualSalary' => "40k\u{a0}£GB",
                'dailySalary' => null,
                'skills' => [
                    [
                        '@id' => '/skills/1',
                        '@type' => 'Skill',
                        'id' => 1,
                        'name' => 'php',
                        'slug' => 'php',
                    ],
                    [
                        '@id' => '/skills/2',
                        '@type' => 'Skill',
                        'id' => 2,
                        'name' => 'java',
                        'slug' => 'java',
                    ],
                    [
                        '@id' => '/skills/3',
                        '@type' => 'Skill',
                        'id' => 3,
                        'name' => 'javascript',
                        'slug' => 'javascript',
                    ],
                ],
                'applicationsCount' => 3,
            ],
            'company' => null,
            'documents' => [
                [
                    'document' => [
                        '@id' => '/user_documents/1',
                        '@type' => 'UserDocument',
                        'document' => getenv('AMAZON_S3_PREFIX') . '/test/users/documents/document1-cm.docx',
                        'resume' => true,
                        'defaultResume' => true,
                    ],
                ],
                [
                    'document' => [
                        '@id' => '/user_documents/2',
                        '@type' => 'UserDocument',
                        'document' => getenv('AMAZON_S3_PREFIX') . '/test/users/documents/document2-cm.docx',
                        'resume' => true,
                        'defaultResume' => false,
                    ],
                ],
            ],
        ]);
    }

    public function testWithValidDataOnCompany(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('POST', '/applications', [
            'json' => [
                'company' => '/companies/company-1',
                'content' => 'Company 1 - Application 2',
                'documents' => [
                    [
                        'document' => '/user_documents/1',
                    ],
                    [
                        'document' => '/user_documents/2',
                    ],
                ],
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/Application',
            '@type' => 'Application',
            'step' => ApplicationStep::RESUME,
            'state' => [
                'value' => 'in_progress',
                'label' => 'Candidature en cours',
            ],
            'content' => 'Company 1 - Application 2',
            'favoriteAt' => null,
            'seenAt' => null,
            'jobPosting' => null,
            'company' => [
                '@id' => '/companies/company-1',
                '@type' => 'Company',
                'id' => 1,
                'name' => 'Company 1',
                'slug' => 'company-1',
                'logo' => [
                    'small' => 'https://api.freework.localhost/media/cache/resolve/company_logo_small/company-1-logo.jpg',
                    'medium' => 'https://api.freework.localhost/media/cache/resolve/company_logo_medium/company-1-logo.jpg',
                ],
            ],
            'documents' => [
                [
                    'document' => [
                        '@id' => '/user_documents/1',
                        '@type' => 'UserDocument',
                        'document' => getenv('AMAZON_S3_PREFIX') . '/test/users/documents/document1-cm.docx',
                        'resume' => true,
                        'defaultResume' => true,
                    ],
                ],
                [
                    'document' => [
                        '@id' => '/user_documents/2',
                        '@type' => 'UserDocument',
                        'document' => getenv('AMAZON_S3_PREFIX') . '/test/users/documents/document2-cm.docx',
                        'resume' => true,
                        'defaultResume' => false,
                    ],
                ],
            ],
        ]);
    }

    public static function provideWithInvalidDataCases(): iterable
    {
        yield [
            'admin@free-work.fr',
            [
                'json' => [
                    'company' => '/companies/company-1',
                    'jobPosting' => '/job_postings/1',
                    'content' => ByteString::fromRandom(601),
                ],
            ],
            [
                '@context' => '/contexts/ConstraintViolationList',
                '@type' => 'ConstraintViolationList',
                'hydra:title' => 'An error occurred',
                'violations' => [
                    [
                        'propertyPath' => '',
                        'message' => 'Application cannot have a company and a jobPosting.',
                    ],
                    [
                        'propertyPath' => 'content',
                        'message' => 'Cette chaîne est trop longue. Elle doit avoir au maximum 600 caractères.',
                    ],
                ],
            ],
        ];

        yield [
            'claude.monet@free-work.fr',
            [
                'json' => [
                    'jobPosting' => '/job_postings/1',
                    'documents' => [],
                ],
            ],
            [
                '@context' => '/contexts/ConstraintViolationList',
                '@type' => 'ConstraintViolationList',
                'hydra:title' => 'An error occurred',
                'violations' => [
                    [
                        'propertyPath' => 'documents',
                        'message' => 'Veuillez joindre un CV à votre candidature.',
                    ],
                ],
            ],
        ];

        yield [
            'claude.monet@free-work.fr',
            [
                'json' => [
                    'jobPosting' => '/job_postings/1',
                    'documents' => [
                        [
                            'document' => '/user_documents/2',
                        ],
                    ],
                ],
            ],
            [
                '@context' => '/contexts/ConstraintViolationList',
                '@type' => 'ConstraintViolationList',
                'hydra:title' => 'An error occurred',
                'violations' => [
                    [
                        'propertyPath' => 'documents',
                        'message' => 'Votre CV partagé doit être joint à votre candidature.',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideWithInvalidDataCases
     */
    public function testWithInvalidData(string $email, array $payload, array $expected): void
    {
        $client = static::createFreeWorkAuthenticatedClient($email);
        $client->request('POST', '/applications', $payload);

        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains($expected);
    }

    public function testWithMissingData(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('POST', '/applications', [
            'json' => [
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'violations' => [
                [
                    'propertyPath' => '',
                    'message' => 'Application must a have at least a company or a jobPosting.',
                ],
            ],
        ]);
    }

    public function testWithForbiddenContent(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('POST', '/applications', [
            'json' => [
                'company' => '/companies/company-1',
                'content' => 'Company 1 - Application 3 forbidden content 1 and forbidden content 2',
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'violations' => [
                [
                    'propertyPath' => 'content',
                    'message' => 'La valeur est constitué d\'élement(s) interdit: "forbidden content 1", "forbidden content 2".',
                ],
            ],
        ]);
    }
}
