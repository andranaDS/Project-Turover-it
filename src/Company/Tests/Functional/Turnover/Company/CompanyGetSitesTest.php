<?php

namespace App\Company\Tests\Functional\Turnover\Company;

use App\Tests\Functional\ApiTestCase;

class CompanyGetSitesTest extends ApiTestCase
{
    public function testBySlugNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/companies/company-1/sites');

        self::assertResponseStatusCodeSame(401);
    }

    public function testBySlugLoggedNotOnMe(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/companies/company-2/sites');

        self::assertResponseStatusCodeSame(403);
    }

    public function testByNotFoundSlugLoggedNotOnMe(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/companies/company-/sites');

        self::assertResponseStatusCodeSame(404);
    }

    public function testBySlugLoggedNotMain(): void
    {
        $client = static::createTurnoverAuthenticatedClient('jesse.pinkman@breaking-bad.com');
        $client->request('GET', '/companies/company-2/sites');

        self::assertResponseStatusCodeSame(403);
    }

    public static function provideBySlugLoggedOnMeCases(): iterable
    {
        return [
            ['/companies/company-1/sites'],
            ['/companies/mine/sites'],
        ];
    }

    /**
     * @dataProvider provideBySlugLoggedOnMeCases
     */
    public function testBySlugLoggedOnMe($path): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', $path);

        self::assertResponseStatusCodeSame(200);
        self::assertJsonContains([
            '@context' => '/contexts/Site',
            '@id' => '/companies/company-1/sites',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/sites/1',
                    '@type' => 'Site',
                    'id' => 1,
                    'name' => 'Site 1 - Company 1',
                    'slug' => 'site-1-company-1',
                    'ip' => '1.1.1.1',
                    'createdAt' => '2022-01-01T10:00:00+01:00',
                    'updatedAt' => '2022-01-01T10:30:00+01:00',
                ],
            ],
            'hydra:totalItems' => 2,
        ]);
    }
}
