<?php

namespace App\JobPosting\Tests\Functional\Turnover\JobPostingShare;

use App\Tests\Functional\ApiTestCase;

class JobPostingSharePostTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('POST', '/job_posting_shares', [
            'json' => [
                'email' => 'guillaume.debailly@le-bureau-des-legendes.fr',
                'jobPosting' => '/job_postings/52',
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testWithValidData(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('POST', '/job_posting_shares', [
            'json' => [
                'email' => 'guillaume.debailly@le-bureau-des-legendes.fr',
                'jobPosting' => '/job_postings/52',
            ],
        ]);
        self::assertResponseStatusCodeSame(201);
        self::assertJsonContains(
            [
                '@context' => '/contexts/JobPostingShare',
                '@type' => 'JobPostingShare',
            ]
        );

        $email = self::getMailerMessage();
        self::assertNotNull($email);
        self::assertEmailHeaderSame($email, 'from', 'Turnover-IT <transfert-demande@turnover-it.com>');
        self::assertEmailHeaderSame($email, 'to', 'guillaume.debailly@le-bureau-des-legendes.fr');
        self::assertEmailHeaderSame($email, 'subject', 'TEST: Offre en sous-traitance de Turnover-IT');
    }

    public function testBadRequest(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('POST', '/job_posting_shares', [
            'json' => [
                'email' => null,
                'jobPosting' => null,
            ],
        ]);
        self::assertResponseStatusCodeSame(400);
        self::assertJsonContains(
            [
                '@context' => '/contexts/Error',
                '@type' => 'hydra:Error',
                'hydra:title' => 'An error occurred',
                'hydra:description' => 'Expected IRI or nested document for attribute "jobPosting", "NULL" given.',
            ]
        );
    }

    public static function provideInvalidCases(): iterable
    {
        yield 'email_not_valid' => [
            [
                'email' => 'guillaume.com',
                'jobPosting' => '/job_postings/52',
            ],
            [
                '@context' => '/contexts/ConstraintViolationList',
                '@type' => 'ConstraintViolationList',
                'hydra:title' => 'An error occurred',
                'violations' => [
                    [
                        'propertyPath' => 'email',
                        'message' => "Cette valeur n'est pas une adresse email valide.",
                    ],
                ],
            ],
        ];

        yield 'email_null' => [
            [
                'email' => null,
                'jobPosting' => '/job_postings/52',
            ],
            [
                '@context' => '/contexts/ConstraintViolationList',
                '@type' => 'ConstraintViolationList',
                'hydra:title' => 'An error occurred',
                'violations' => [
                    [
                        'propertyPath' => 'email',
                        'message' => 'Cette valeur ne doit pas être vide.',
                    ],
                ],
            ],
        ];

        yield 'contracts_not_intercontract' => [
            [
                'email' => 'guillaume.debailly@le-bureau-des-legendes.fr',
                'jobPosting' => '/job_postings/1',
            ],
            [
                '@context' => '/contexts/ConstraintViolationList',
                '@type' => 'ConstraintViolationList',
                'hydra:title' => 'An error occurred',
                'violations' => [
                    [
                        'propertyPath' => 'jobPosting.contracts',
                        'message' => "L'offre n'est pas de type intercontrat.",
                    ],
                ],
            ],
        ];

        yield 'not_published' => [
            [
                'email' => 'guillaume.debailly@le-bureau-des-legendes.fr',
                'jobPosting' => '/job_postings/55',
            ],
            [
                '@context' => '/contexts/ConstraintViolationList',
                '@type' => 'ConstraintViolationList',
                'hydra:title' => 'An error occurred',
                'violations' => [
                    [
                        'propertyPath' => 'jobPosting.status',
                        'message' => "L'offre n'est pas publiée.",
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
        $client->request('POST', '/job_posting_shares', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains($expected);
    }
}
