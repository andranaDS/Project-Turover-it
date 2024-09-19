<?php

namespace App\JobPosting\Tests\Functional\Turnover\JobPosting;

use App\Tests\Functional\ApiTestCase;
use Nette\Utils\Json;

class JobPostingsGetCountTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();

        $client->request('GET', '/job_postings');

        self::assertResponseStatusCodeSame(401);
    }

    public static function provideLoggedCases(): iterable
    {
        yield [
            'filters' => [],
            'expected' => 4,
        ];

        yield [
            'filters' => [
                'minDuration' => 4,
            ],
            'expected' => 1,
        ];

        yield [
            'filters' => [
                'maxDuration' => 3,
            ],
            'expected' => 1,
        ];

        yield [
            'filters' => [
                'locations' => 'fr~ile-de-france,fr~hauts-de-france~pas-de-calais~le-touquet-paris-plage',
            ],
            'expected' => 3,
        ];

        yield [
            'filters' => [
                'locations' => 'fr~hauts-de-france~pas-de-calais~le-touquet-paris-plage',
            ],
            'expected' => 1,
        ];

        yield [
            'filters' => [
                'minDailySalary' => 600,
            ],
            'expected' => 2,
        ];

        yield [
            'filters' => [
                'maxDailySalary' => 800,
            ],
            'expected' => 2,
        ];

        yield [
            'filters' => [
                'remoteMode' => 'full',
            ],
            'expected' => 2,
        ];

        yield [
            'filters' => [
                'keywords' => 'Zend',
            ],
            'expected' => 1,
        ];

        yield [
            'filters' => [
                'businessActivity' => 'business-activity-1',
            ],
            'expected' => 2,
        ];

        yield [
            'filters' => [
                'intercontractOnly' => 'true',
            ],
            'expected' => 3,
        ];

        yield [
            'filters' => [
                'intercontractOnly' => 'false',
            ],
            'expected' => 4,
        ];

        yield [
            'filters' => [
                'order' => 'relevance',
                'keywords' => 'java',
            ],
            'expected' => 4,
        ];

        yield [
            'filters' => [
                'order' => 'date',
            ],
            'expected' => 4,
        ];

        yield [
            'filters' => [
                'order' => 'salary',
            ],
            'expected' => 4,
        ];
    }

    /**
     * @dataProvider provideLoggedCases
     */
    public function testLogged(array $filters, int $expected): void
    {
        $client = static::createTurnoverAuthenticatedClient('jesse.pinkman@breaking-bad.com');

        $response = $client->request('GET', '/job_postings/count?' . http_build_query($filters));

        self::assertResponseIsSuccessful();
        self::assertSame($expected, Json::decode($response->getContent()));
    }
}
