<?php

namespace App\Folder\Tests\Functional\Folder;

use App\Tests\Functional\ApiTestCase;

class FolderGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createTurnoverClient();
        $client->request('GET', '/folders/1');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsNotOwner(): void
    {
        $client = static::createTurnoverAuthenticatedClient('eddard.stark@got.com');
        $client->request('GET', '/folders/1');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsOwner(): void
    {
        $client = static::createTurnoverAuthenticatedClient();
        $client->request('GET', '/folders/4');

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/contexts/Folder',
            '@id' => '/folders/4',
            '@type' => 'Folder',
            'id' => 4,
            'name' => null,
            'type' => 'favorites',
            'usersCount' => 3,
            'users' => [
                [
                    '@type' => 'FolderUser',
                    'id' => 1,
                    'user' => [
                        '@id' => '/users/2',
                        '@type' => 'User',
                        'id' => 2,
                        'firstName' => 'Admin',
                        'lastName' => 'Free-Work',
                        'jobTitle' => null,
                    ],
                ],
                [
                    '@type' => 'FolderUser',
                    'id' => 2,
                    'user' => [
                        '@id' => '/users/3',
                        '@type' => 'User',
                        'id' => 3,
                        'firstName' => 'User Forgotten Password With Active Request',
                        'lastName' => 'Free-Work',
                        'jobTitle' => null,
                    ],
                ],
                [
                    '@type' => 'FolderUser',
                    'id' => 3,
                    'user' => [
                        '@id' => '/users/4',
                        '@type' => 'User',
                        'id' => 4,
                        'firstName' => 'User Forgotten Password With Expired Request',
                        'lastName' => 'Free-Work',
                        'jobTitle' => null,
                    ],
                ],
            ],
        ]);
    }
}
