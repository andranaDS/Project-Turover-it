<?php

namespace App\Core\Tests\Functional\Alert;

use App\Tests\Functional\ApiTestCase;

class AlertGetTest extends ApiTestCase
{
    public static function provideLoggedAndNotLoggedCases(): iterable
    {
        yield ['user@free-work.fr'];
        yield ['admin@free-work.fr'];
        yield [null];
    }

    /**
     * @dataProvider provideLoggedAndNotLoggedCases
     */
    public function testLoggedAndNotLogged(?string $email): void
    {
        if (null !== $email) {
            $client = self::createFreeWorkAuthenticatedClient($email);
        } else {
            $client = self::createFreeWorkClient();
        }

        $client->request('GET', '/alerts/1');

        self::assertResponseStatusCodeSame(410);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public static function provideWithExistingCases(): iterable
    {
        yield [
            1,
            [
                '@context' => '/contexts/Alert',
                '@id' => '/alerts/1',
                'id' => 1,
                'contentHtml' => '<p>Alert 1 - Content</p>',
                'blocking' => false,
                'startAt' => '2021-01-10T21:00:00+01:00',
                'endAt' => '2021-01-20T21:00:00+01:00',
            ],
        ];
    }

    /**
     * @dataProvider provideWithExistingCases
     */
    public function testWithExisting(int $alertId, array $expected): void
    {
        $client = self::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/alerts/' . $alertId);

        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains($expected);
    }

    public static function provideWithActiveCases(): iterable
    {
        yield [
            3,
            [
                '@context' => '/contexts/Alert',
                '@id' => '/alerts/3',
                'id' => 3,
                'contentHtml' => '<p>Alert 3 - Content Active</p>',
                'blocking' => false,
                'startAt' => '2021-11-10T21:00:00+01:00',
                'endAt' => (new \DateTime())->modify('+1 day')->setTime(0, 0)->format(\DateTime::RFC3339),
            ],
        ];
    }

    /**
     * @dataProvider provideWithActiveCases
     */
    public function testWithActive(int $alertId, array $expected): void
    {
        $client = self::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/alerts/3');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertResponseHasHeader('X-Alert', 3);
        self::assertJsonContains($expected);
    }
}
