<?php

namespace App\Company\Tests\Functional\Turnover\Company;

use App\Tests\Functional\ApiTestCase;

class CompanyGetRecruitersTest extends ApiTestCase
{
    public function testBySlugNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/companies/company-1/recruiters');

        self::assertResponseStatusCodeSame(401);
    }

    public function testBySlugLoggedNotOnMe(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/companies/company-2/recruiters');

        self::assertResponseStatusCodeSame(403);
    }

    public function testByNotFoundSlugLoggedNotOnMe(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/companies/company-/recruiters');

        self::assertResponseStatusCodeSame(404);
    }

    public function testBySlugLoggedNotMain(): void
    {
        $client = static::createTurnoverAuthenticatedClient('jesse.pinkman@breaking-bad.com');
        $client->request('GET', '/companies/company-1/recruiters');

        self::assertResponseStatusCodeSame(200);
    }

    public static function provideBySlugLoggedOnMeCases(): iterable
    {
        return [
            ['/companies/company-1/recruiters?main=0'],
            ['/companies/mine/recruiters?main=0'],
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
            '@context' => '/contexts/Recruiter',
            '@id' => '/companies/company-1/recruiters',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/recruiters/2',
                    '@type' => 'Recruiter',
                    'id' => 2,
                    'email' => 'jesse.pinkman@breaking-bad.com',
                    'username' => 'jesse.pinkman',
                    'gender' => 'male',
                    'firstName' => 'Jesse',
                    'lastName' => 'Pinkman',
                    'phoneNumber' => '+33687654321',
                    'site' => [
                        '@id' => '/sites/1',
                        '@type' => 'Site',
                        'id' => 1,
                        'name' => 'Site 1 - Company 1',
                        'slug' => 'site-1-company-1',
                    ],
                ],
                [
                    '@id' => '/recruiters/3',
                    '@type' => 'Recruiter',
                    'id' => 3,
                    'email' => 'gustavo.fring@breaking-bad.com',
                    'username' => 'gustavo.fring',
                    'gender' => null,
                    'firstName' => 'Gustavo',
                    'lastName' => 'Fring',
                    'phoneNumber' => null,
                    'site' => [
                        '@id' => '/sites/1',
                        '@type' => 'Site',
                        'id' => 1,
                        'name' => 'Site 1 - Company 1',
                        'slug' => 'site-1-company-1',
                    ],
                ],
            ],
            'hydra:totalItems' => 2,
        ]);
    }
}
