<?php

namespace App\User\Tests\Functional\UserDocument;

use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;
use App\User\Entity\UserDocument;
use Doctrine\ORM\EntityManagerInterface;

class UserDocumentPutTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('PUT', '/user_documents/1', [
            'json' => [
                'defaultResume' => true,
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('PUT', '/user_documents/1', [
            'json' => [
                'defaultResume' => true,
            ],
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('PUT', '/user_documents/1', [
            'json' => [
                'defaultResume' => true,
            ],
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsUserOnNonExistantDocument(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('PUT', '/user_documents/non-existant-document', [
            'json' => [
                'defaultResume' => true,
            ],
        ]);

        self::assertResponseStatusCodeSame(404);
    }

    public function testLoggedAsUserOnItsOwnEntity(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();

        // 1 - before
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'claude.monet@free-work.fr',
        ]);
        $userDocument = $em->getRepository(UserDocument::class)->findOneBy([
            'user' => $user,
            'defaultResume' => true,
        ]);

        self::assertNotNull($user);
        self::assertNotNull($userDocument);
        self::assertSame(1, $userDocument->getId());

        // 2 - put defaultResume on UserDocument
        $client->request('PUT', '/user_documents/2', [
            'json' => [
                'defaultResume' => true,
            ],
        ]);

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
            'defaultResume' => true,
            'createdAt' => '2021-01-02T10:00:00+01:00',
        ]);

        // 3 - after
        $userDocument = $em->getRepository(UserDocument::class)->findOneBy([
            'user' => $user,
            'defaultResume' => true,
        ]);
        self::assertNotNull($userDocument);
        self::assertSame(2, $userDocument->getId());
    }
}
