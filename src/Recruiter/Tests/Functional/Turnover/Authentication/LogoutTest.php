<?php

namespace App\Recruiter\Tests\Functional\Turnover\Authentication;

use App\Tests\Functional\ApiTestCase;

class LogoutTest extends ApiTestCase
{
    public function test(): void
    {
        $client = static::createTurnoverClient();

        $client->request('GET', '/recruiters/me');
        self::assertResponseStatusCodeSame(404);

        $client->request('POST', '/login', [
            'json' => [
                'email' => 'walter.white@breaking-bad.com',
                'password' => 'P@ssw0rd',
            ],
        ]);

        $client->request('GET', '/recruiters/me');
        self::assertResponseStatusCodeSame(200);

        $client->request('GET', '/logout');
        self::assertResponseStatusCodeSame(200);

        $client->request('GET', '/recruiters/me');
        self::assertResponseStatusCodeSame(404);
    }
}
