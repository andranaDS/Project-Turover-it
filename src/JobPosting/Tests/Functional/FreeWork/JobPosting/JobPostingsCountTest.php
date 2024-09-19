<?php

namespace App\JobPosting\Tests\Functional\FreeWork\JobPosting;

use App\Tests\Functional\ApiTestCase;
use Nette\Utils\Json;

class JobPostingsCountTest extends ApiTestCase
{
    protected function setUp(): void
    {
        self::synchronizeElasticsearch();
    }

    public static function provideCountCases(): iterable
    {
        yield [
            null,
            'count' => 51,
        ];
        yield [
            'locationKeys=fr~ile-de-france~~',
            'count' => 24,
        ];
        yield [
            'locationKeys=fr~provence-alpes-cote-d-azur~~marseille',
            'count' => 0,
        ];
    }

    /**
     * @dataProvider provideCountCases
     */
    public function testCount(?string $filters, int $count): void
    {
        $client = static::createFreeWorkClient();

        $response = $client->request('GET', '/job_postings/count?' . $filters ?? '');

        self::assertSame($count, Json::decode($response->getContent()));
    }
}
