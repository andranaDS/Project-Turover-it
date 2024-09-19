<?php

namespace App\Notification\Tests\Functional\Turnover\Notification;

use App\Tests\Functional\ApiTestCase;
use Carbon\Carbon;

class NotificationsGetItemTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/notifications/1');

        self::assertResponseStatusCodeSame(401);
    }

    public function testNotFound(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/notifications/not-found');

        self::assertResponseStatusCodeSame(404);
    }

    public function testLoggedAsNotOwner(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/notifications/1');

        self::assertResponseStatusCodeSame(403);
    }

    public static function provideLoggedAsOwnerCases(): iterable
    {
        yield [
            'path' => '/notifications/3',
            'expected' => [
                '@context' => '/contexts/Notification',
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
        ];

        yield [
            'path' => '/notifications/4',
            'expected' => [
                '@context' => '/contexts/Notification',
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
        ];

        yield [
            'path' => '/notifications/5',
            'expected' => [
                '@context' => '/contexts/Notification',
                '@id' => '/notifications/5',
                '@type' => 'Notification',
                'id' => 5,
                'createdAt' => Carbon::today()->subDays(4)->setTime(10, 00, 00)->format(\DateTimeInterface::RFC3339),
                'event' => 'subscription_ending_soon',
                'data' => [],
                'read' => true,
            ],
        ];
    }

    /**
     * @dataProvider provideLoggedAsOwnerCases
     */
    public function testLoggedAsOwner(string $path, array $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', $path);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains($expected);
    }
}
