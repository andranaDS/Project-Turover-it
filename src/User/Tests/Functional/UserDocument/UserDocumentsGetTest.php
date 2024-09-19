<?php

namespace App\User\Tests\Functional\UserDocument;

use App\Tests\Functional\ApiTestCase;

class UserDocumentsGetTest extends ApiTestCase
{
    public static function provideLoggedAndNotLoggedCases(): iterable
    {
        yield ['user@free-work.fr', 200];
        yield ['admin@free-work.fr', 200];
        yield [null, 401];
    }

    /**
     * @dataProvider provideLoggedAndNotLoggedCases
     */
    public function testLoggedAndNotLogged(?string $email, int $statusCode): void
    {
        if (null !== $email) {
            $client = self::createFreeWorkAuthenticatedClient($email);
        } else {
            $client = self::createFreeWorkClient();
        }

        $client->request('GET', '/user_documents');

        self::assertResponseStatusCodeSame($statusCode);
    }

    public function testCase(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr'); // id 6
        $client->request('GET', '/user_documents');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/UserDocument',
            '@id' => '/user_documents',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/user_documents/1',
                    '@type' => 'UserDocument',
                    'id' => 1,
                    'originalName' => 'user-document-1.docx',
                    'document' => getenv('AMAZON_S3_PREFIX') . '/test/users/documents/document1-cm.docx',
                    'resume' => true,
                    'defaultResume' => true,
                    'createdAt' => '2021-01-01T10:00:00+01:00',
                    'updatedAt' => '2021-01-01T20:00:00+01:00',
                ],
                [
                    '@id' => '/user_documents/3',
                    '@type' => 'UserDocument',
                    'id' => 3,
                    'originalName' => 'user-document-3.pdf',
                    'document' => getenv('AMAZON_S3_PREFIX') . '/test/users/documents/document3-cm.pdf',
                    'resume' => false,
                    'defaultResume' => false,
                    'createdAt' => '2021-01-03T10:00:00+01:00',
                    'updatedAt' => '2021-01-03T20:00:00+01:00',
                ],
            ],
            'hydra:totalItems' => 3,
            'hydra:view' => [
                '@id' => '/user_documents?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/user_documents?page=1',
                'hydra:last' => '/user_documents?page=2',
                'hydra:next' => '/user_documents?page=2',
            ],
        ]);
    }
}
