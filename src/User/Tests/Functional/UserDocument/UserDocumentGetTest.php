<?php

namespace App\User\Tests\Functional\UserDocument;

use App\Tests\Functional\ApiTestCase;

class UserDocumentGetTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/user_documents/1');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('GET', '/user_documents/1');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('GET', '/user_documents/1');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsUserOnNonExistantDocument(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/user_documents/non-existant-document');

        self::assertResponseStatusCodeSame(404);
    }

    public function testLoggedAsUserOnItsOwnEntity(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('GET', '/user_documents/2');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/UserDocument',
            '@id' => '/user_documents/2',
            '@type' => 'UserDocument',
            'id' => 2,
            'originalName' => 'user-document-2.docx',
            'document' => getenv('AMAZON_S3_PREFIX') . '/test/users/documents/document2-cm.docx',
            'resume' => true,
            'defaultResume' => false,
            'createdAt' => '2021-01-02T10:00:00+01:00',
        ]);
    }
}
