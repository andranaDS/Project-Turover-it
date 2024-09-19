<?php

namespace App\Notification\Tests\Functional\Turnover\Notification;

use App\Tests\Functional\ApiTestCase;
use Carbon\Carbon;

class NotificationsGetCollectionTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/notifications');
        self::assertResponseStatusCodeSame(401);
    }

    public function provideLoggedCases(): iterable
    {
        // primary
        yield [
            'email' => 'walter.white@breaking-bad.com',
            'parameters' => [],
            'expected' => [
                '@context' => '/contexts/Notification',
                '@id' => '/notifications',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    $this->getNotification(7),
                    $this->getNotification(6),
                ],
                'hydra:totalItems' => 5,
                'hydra:view' => [
                    '@id' => '/notifications?page=1',
                    '@type' => 'hydra:PartialCollectionView',
                    'hydra:first' => '/notifications?page=1',
                    'hydra:last' => '/notifications?page=3',
                    'hydra:next' => '/notifications?page=2',
                ],
                'hydra:search' => [
                    '@type' => 'hydra:IriTemplate',
                    'hydra:template' => '/notifications{?id[between],id[gt],id[gte],id[lt],id[lte],event,event[]}',
                    'hydra:variableRepresentation' => 'BasicRepresentation',
                    'hydra:mapping' => [
                        [
                            '@type' => 'IriTemplateMapping',
                            'variable' => 'id[between]',
                            'property' => 'id',
                            'required' => false,
                        ],
                        [
                            '@type' => 'IriTemplateMapping',
                            'variable' => 'id[gt]',
                            'property' => 'id',
                            'required' => false,
                        ],
                        [
                            '@type' => 'IriTemplateMapping',
                            'variable' => 'id[gte]',
                            'property' => 'id',
                            'required' => false,
                        ],
                        [
                            '@type' => 'IriTemplateMapping',
                            'variable' => 'id[lt]',
                            'property' => 'id',
                            'required' => false,
                        ],
                        [
                            '@type' => 'IriTemplateMapping',
                            'variable' => 'id[lte]',
                            'property' => 'id',
                            'required' => false,
                        ],
                        [
                            '@type' => 'IriTemplateMapping',
                            'variable' => 'event',
                            'property' => 'event',
                            'required' => false,
                        ],
                        [
                            '@type' => 'IriTemplateMapping',
                            'variable' => 'event[]',
                            'property' => 'event',
                            'required' => false,
                        ],
                    ],
                ],
            ],
        ];

        // secondary
        yield [
            'email' => 'jesse.pinkman@breaking-bad.com',
            'parameters' => [],
            'expected' => [
                '@context' => '/contexts/Notification',
                '@id' => '/notifications',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    $this->getNotification(2),
                    $this->getNotification(1),
                ],
                'hydra:totalItems' => 2,
            ],
        ];

        // pagination
        yield [
            'email' => 'walter.white@breaking-bad.com',
            'parameters' => [
                'itemsPerPage' => 3,
            ],
            'expected' => [
                '@context' => '/contexts/Notification',
                '@id' => '/notifications',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    $this->getNotification(7),
                    $this->getNotification(6),
                    $this->getNotification(5),
                ],
                'hydra:totalItems' => 5,
                'hydra:view' => [
                    '@id' => '/notifications?itemsPerPage=3&page=1',
                    '@type' => 'hydra:PartialCollectionView',
                    'hydra:first' => '/notifications?itemsPerPage=3&page=1',
                    'hydra:last' => '/notifications?itemsPerPage=3&page=2',
                    'hydra:next' => '/notifications?itemsPerPage=3&page=2',
                ],
            ],
        ];

        // id less than
        yield [
            'email' => 'walter.white@breaking-bad.com',
            'parameters' => [
                'id[lt]' => 6,
            ],
            'expected' => [
                '@context' => '/contexts/Notification',
                '@id' => '/notifications',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    $this->getNotification(5),
                    $this->getNotification(4),
                ],
                'hydra:totalItems' => 3,
                'hydra:view' => [
                    '@id' => '/notifications?id%5Blt%5D=6&page=1',
                    '@type' => 'hydra:PartialCollectionView',
                    'hydra:first' => '/notifications?id%5Blt%5D=6&page=1',
                    'hydra:last' => '/notifications?id%5Blt%5D=6&page=2',
                    'hydra:next' => '/notifications?id%5Blt%5D=6&page=2',
                ],
            ],
        ];

        yield [
            'email' => 'walter.white@breaking-bad.com',
            'parameters' => [
                'event' => 'job_posting_draft_expiring_soon',
            ],
            'expected' => [
                '@context' => '/contexts/Notification',
                '@id' => '/notifications',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    $this->getNotification(4),
                ],
                'hydra:totalItems' => 1,
                'hydra:view' => [
                    '@id' => '/notifications?event=job_posting_draft_expiring_soon',
                    '@type' => 'hydra:PartialCollectionView',
                ],
            ],
        ];

        yield [
            'email' => 'walter.white@breaking-bad.com',
            'parameters' => [
                'event' => 'job_posting_draft_expiring_soon,subscription_ending_soon',
            ],
            'expected' => [
                '@context' => '/contexts/Notification',
                '@id' => '/notifications',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    $this->getNotification(6),
                    $this->getNotification(5),
                ],
                'hydra:totalItems' => 3,
                'hydra:view' => [
                    '@id' => '/notifications?event=job_posting_draft_expiring_soon%2Csubscription_ending_soon&page=1',
                    '@type' => 'hydra:PartialCollectionView',
                    'hydra:first' => '/notifications?event=job_posting_draft_expiring_soon%2Csubscription_ending_soon&page=1',
                    'hydra:last' => '/notifications?event=job_posting_draft_expiring_soon%2Csubscription_ending_soon&page=2',
                    'hydra:next' => '/notifications?event=job_posting_draft_expiring_soon%2Csubscription_ending_soon&page=2',
                ],
            ],
        ];

        yield [
            'email' => 'walter.white@breaking-bad.com',
            'parameters' => [
                'event' => [
                    'job_posting_draft_expiring_soon',
                    'subscription_ending_soon',
                ],
            ],
            'expected' => [
                '@context' => '/contexts/Notification',
                '@id' => '/notifications',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    $this->getNotification(6),
                    $this->getNotification(5),
                ],
                'hydra:totalItems' => 3,
                'hydra:view' => [
                    '@id' => '/notifications?event%5B%5D=job_posting_draft_expiring_soon&event%5B%5D=subscription_ending_soon&page=1',
                    '@type' => 'hydra:PartialCollectionView',
                    'hydra:first' => '/notifications?event%5B%5D=job_posting_draft_expiring_soon&event%5B%5D=subscription_ending_soon&page=1',
                    'hydra:last' => '/notifications?event%5B%5D=job_posting_draft_expiring_soon&event%5B%5D=subscription_ending_soon&page=2',
                    'hydra:next' => '/notifications?event%5B%5D=job_posting_draft_expiring_soon&event%5B%5D=subscription_ending_soon&page=2',
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideLoggedCases
     */
    public function testLogged(string $email, array $parameters, array $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient($email);

        $path = '/notifications?' . http_build_query($parameters);
        $client->request('GET', $path);

        self::assertResponseIsSuccessful();
        self::assertJsonContains($expected);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function getNotification(int $id): ?array
    {
        $notifications = [
            1 => [
                '@id' => '/notifications/1',
                '@type' => 'Notification',
                'id' => 1,
                'createdAt' => '2022-03-01T10:00:00+01:00',
                'event' => 'application_new',
                'data' => [
                    'application' => [
                        '@id' => '/applications/5',
                        '@type' => 'Application',
                    ],
                ],
                'read' => true,
            ],
            2 => [
                '@id' => '/notifications/2',
                '@type' => 'Notification',
                'id' => 2,
                'createdAt' => '2022-04-01T10:00:00+02:00',
                'event' => 'subscription_ending_soon',
                'data' => [
                ],
                'read' => false,
            ],
            3 => [
                '@id' => '/notifications/3',
                '@type' => 'Notification',
                'id' => 3,
                'createdAt' => Carbon::today()->subDays(60)->setTime(10, 00, 00)->format(\DateTimeInterface::RFC3339),
                'event' => 'application_new',
                'data' => [
                    'application' => [
                        '@id' => '/applications/1',
                        '@type' => 'Application',
                    ],
                ],
                'read' => true,
            ],
            4 => [
                '@id' => '/notifications/4',
                '@type' => 'Notification',
                'id' => 4,
                'createdAt' => Carbon::today()->subDays(10)->setTime(10, 00, 00)->format(\DateTimeInterface::RFC3339),
                'event' => 'job_posting_draft_expiring_soon',
                'data' => [
                    'jobPosting' => [
                        '@id' => '/job_postings/37',
                        '@type' => 'JobPosting',
                        'id' => 37,
                        'title' => 'Comptable Fournisseur h/f',
                        'slug' => 'comptable-fournisseur-h-f-1',
                    ],
                ],
                'read' => true,
            ],
            5 => [
                '@id' => '/notifications/5',
                '@type' => 'Notification',
                'id' => 5,
                'createdAt' => Carbon::today()->subDays(4)->setTime(10, 00, 00)->format(\DateTimeInterface::RFC3339),
                'event' => 'subscription_ending_soon',
                'data' => [],
                'read' => true,
            ],
            6 => [
                '@id' => '/notifications/6',
                '@type' => 'Notification',
                'id' => 6,
                'createdAt' => Carbon::yesterday()->setTime(12, 00, 00)->format(\DateTimeInterface::RFC3339),
                'event' => 'subscription_ending_soon',
                'data' => [],
                'read' => false,
            ],
            7 => [
                '@id' => '/notifications/7',
                '@type' => 'Notification',
                'id' => 7,
                'createdAt' => Carbon::today()->setTime(00, 30, 00)->format(\DateTimeInterface::RFC3339),
                'event' => 'application_abandoned',
                'data' => [
                    'application' => [
                        '@id' => '/applications/4',
                        '@type' => 'Application',
                    ],
                ],
                'read' => false,
            ],
        ];

        return $notifications[$id] ?? null;
    }
}
