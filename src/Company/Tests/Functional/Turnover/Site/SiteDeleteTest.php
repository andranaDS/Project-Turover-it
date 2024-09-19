<?php

namespace App\Company\Tests\Functional\Turnover\Site;

use App\Tests\Functional\ApiTestCase;

class SiteDeleteTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('DELETE', '/sites/1');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedWithoutMainAccount(): void
    {
        $client = static::createTurnoverAuthenticatedClient('jesse.pinkman@breaking-bad.com');
        $client->request('DELETE', '/sites/1');

        self::assertResponseStatusCodeSame(403);
    }

    public function testErrorLoggedAsAuthorizeUser(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('DELETE', '/sites/1');

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains([
            'hydra:title' => 'Le site ne doit plus avoir de compte liés.',
        ]);
    }

    public function testSuccessLoggedAsAuthorizeUser(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('DELETE', '/sites/2');

        self::assertResponseStatusCodeSame(204);
    }
}
