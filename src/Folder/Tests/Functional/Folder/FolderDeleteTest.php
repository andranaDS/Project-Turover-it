<?php

namespace App\Folder\Tests\Functional\Folder;

use App\Tests\Functional\ApiTestCase;

class FolderDeleteTest extends ApiTestCase
{
    public function testNotFound(): void
    {
        $client = static::createTurnoverAuthenticatedClient('eddard.stark@got.com');

        $client->request('DELETE', '/folders/not-found');
        self::assertResponseStatusCodeSame(404);
    }

    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('DELETE', '/folders/1');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsNotOwner(): void
    {
        $client = static::createTurnoverAuthenticatedClient('eddard.stark@got.com');
        $client->request('DELETE', '/folders/1');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsOwner(): void
    {
        $client = static::createTurnoverAuthenticatedClient('eddard.stark@got.com');
        $client->request('DELETE', '/folders/24');
        self::assertResponseStatusCodeSame(204);

        $client->request('DELETE', '/folders/24');
        self::assertResponseStatusCodeSame(404);
    }
}
