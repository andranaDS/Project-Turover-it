<?php

namespace App\Recruiter\Tests\Functional\Turnover\RecruiterJob;

use App\Tests\Functional\ApiTestCase;

class GetItemTest extends ApiTestCase
{
    public static function provideExistsCases(): iterable
    {
        return [
            [
                '/recruiter_jobs/1',
                [
                    '@context' => '/contexts/RecruiterJob',
                    '@id' => '/recruiter_jobs/1',
                    '@type' => 'RecruiterJob',
                    'id' => 1,
                    'name' => 'Account manager',
                ],
            ],
            [
                '/recruiter_jobs/2',
                [
                    '@context' => '/contexts/RecruiterJob',
                    '@id' => '/recruiter_jobs/2',
                    '@type' => 'RecruiterJob',
                    'id' => 2,
                    'name' => 'Architecte',
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideExistsCases
     */
    public function testExists(string $query, array $expected): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', $query);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonEquals($expected);
    }

    public function testNotExists(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/recruiter_jobs/1337');

        self::assertResponseStatusCodeSame(404);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}
