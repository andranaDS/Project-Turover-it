<?php

namespace App\User\Tests\Functional\CompanyCountryCode;

use App\Tests\Functional\ApiTestCase;

class GetTest extends ApiTestCase
{
    public function testCases(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/company_country_codes');

        self::assertJsonContains([
            'FR' => 'France',
            'AF' => 'Afghanistan',
            'ZA' => 'Afrique du Sud',
            'AL' => 'Albanie',
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');
    }
}
