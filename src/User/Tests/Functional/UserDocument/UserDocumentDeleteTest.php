<?php

namespace App\User\Tests\Functional\UserDocument;

use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserDocumentDeleteTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('DELETE', '/user_documents/1');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('DELETE', '/user_documents/1');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsOwner(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('DELETE', '/user_documents/1');

        self::assertResponseIsSuccessful();
    }

    public function testLoggedAsNotOwner(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('vincent.van-gogh@free-work.fr');
        $client->request('DELETE', '/user_documents/1');

        self::assertResponseStatusCodeSame(403);
    }

    public function testNotDeleted(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $client->request('GET', '/user_documents/1');

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/contexts/UserDocument',
            '@type' => 'UserDocument',
            'document' => getenv('AMAZON_S3_PREFIX') . '/test/users/documents/document1-cm.docx',
        ]);

        $client->request('DELETE', '/user_documents/1');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/user_documents/1');
        self::assertResponseStatusCodeSame(404);
    }

    /**
     * @dataProvider provideDeleteUniqueDefaultResumeCases
     */
    public function testDeleteUniqueDefaultResume(string $email, int $documentId): void
    {
        $client = static::createFreeWorkAuthenticatedClient($email);

        if (null === $container = $client->getContainer()) {
            throw new \RuntimeException('Container is null');
        }

        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->findOneByEmail($email);

        self::assertNotNull($user->getFormStep());

        $client->request('DELETE', '/user_documents/' . $documentId);

        self::assertResponseIsSuccessful();
        self::assertNull($user->getFormStep());
        self::assertFalse($user->getProfileCompleted());
    }

    public static function provideDeleteUniqueDefaultResumeCases(): iterable
    {
        yield ['vincent.van-gogh@free-work.fr', 4];
    }
}
