<?php

namespace App\User\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;

class UserGetPartnerTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();

        $client->request('GET', '/user_partner');
        self::assertResponseStatusCodeSame(401);
    }

    public static function provideLoggedAsUserCases(): iterable
    {
        // No Partner
        yield ['pablo.picasso@free-work.fr', ['sent' => false]];

        // Partner and no lead
        yield ['vincent.van-gogh@free-work.fr', ['sent' => false]];

        // Partner and lead OK
        yield ['claude.monet@free-work.fr', ['sent' => true]];

        // Partner and lead KO
        yield ['auguste.renoir@free-work.fr', ['sent' => false]];
    }

    /**
     * @dataProvider provideLoggedAsUserCases
     */
    public function testLoggedAsUser(string $email, array $expected): void
    {
        $client = static::createFreeWorkAuthenticatedClient($email);
        $client->request('GET', '/user_partner');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');
        self::assertJsonContains($expected);
    }
}
