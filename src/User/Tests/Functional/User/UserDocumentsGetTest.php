<?php

namespace App\User\Tests\Functional\User;

use App\Tests\Functional\ApiTestCase;

class UserDocumentsGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/users/1/documents');

        self::assertResponseStatusCodeSame(401);
    }

    public function testWithNonExistentUser(): void
    {
        $client = static::createFreeWorkAuthenticatedClient();
        $client->request('GET', '/users/user-non-existent/documents');

        self::assertResponseStatusCodeSame(403);
    }

    public function testOnInOtherEntity(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();

        $client->request('GET', '/users/3/documents');

        self::assertResponseStatusCodeSame(403);
    }

    public function testWithoutDocuments(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('elisabeth.vigee-le-brun@free-work.fr');
        $client->request('GET', '/users/11/documents');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/UserDocument',
            '@id' => '/users/11/documents',
            '@type' => 'hydra:Collection',
            'hydra:member' => [],
            'hydra:totalItems' => 0,
        ]);
    }

    public function testWithDocuments(): void
    {
        $client = self::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/users/6/documents');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/UserDocument',
            '@id' => '/users/6/documents',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@type' => 'UserDocument',
                    'document' => getenv('AMAZON_S3_PREFIX') . '/test/users/documents/document1-cm.docx',
                    'resume' => true,
                    'defaultResume' => true,
                    'createdAt' => '2021-01-01T10:00:00+01:00',
                    'updatedAt' => '2021-01-01T20:00:00+01:00',
                ],
                [
                    '@type' => 'UserDocument',
                    'document' => getenv('AMAZON_S3_PREFIX') . '/test/users/documents/document3-cm.pdf',
                    'resume' => false,
                    'defaultResume' => false,
                    'createdAt' => '2021-01-03T10:00:00+01:00',
                    'updatedAt' => '2021-01-03T20:00:00+01:00',
                ],
            ],
            'hydra:totalItems' => 3,
        ]);
    }
}
