<?php

namespace App\Forum\Tests\Functional\ForumPost;

use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ForumPostDeleteTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('DELETE', '/forum_posts/2');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('DELETE', '/forum_posts/2');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsOwner(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('vincent.van-gogh@free-work.fr');
        $client->request('DELETE', '/forum_posts/2');

        self::assertResponseIsSuccessful();
    }

    public function testLoggedAsNotOwner(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('DELETE', '/forum_posts/2');

        self::assertResponseStatusCodeSame(403);
    }

    public function testLeaf(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('vincent.van-gogh@free-work.fr');
        $client->request('GET', '/forum_posts/2');

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/contexts/ForumPost',
            '@type' => 'ForumPost',
            'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 2</p>',
            'deleted' => false,
        ]);

        $client->request('DELETE', '/forum_posts/2');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/forum_posts/2');
        self::assertResponseStatusCodeSame(404);
    }

    public function testNotLeaf(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('auguste.renoir@free-work.fr');
        $client->request('GET', '/forum_posts/4');

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/contexts/ForumPost',
            '@type' => 'ForumPost',
            'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2 - Lorem</p>',
            'deleted' => false,
        ]);

        $client->request('DELETE', '/forum_posts/4');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/forum_posts/4');
        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/contexts/ForumPost',
            '@type' => 'ForumPost',
            'contentHtml' => null,
            'deleted' => true,
        ]);
    }

    public function testNotDeleted(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('vincent.van-gogh@free-work.fr');
        $client->request('GET', '/forum_posts/2');

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            '@context' => '/contexts/ForumPost',
            '@type' => 'ForumPost',
            'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 2</p>',
            'deleted' => false,
        ]);

        $client->request('DELETE', '/forum_posts/2');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/forum_posts/2');
        self::assertResponseStatusCodeSame(404);
    }

    public function testAlreadyDeletedNotHidden(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('vincent.van-gogh@free-work.fr');
        $client->request('GET', '/forum_posts/20');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumPost',
            '@type' => 'ForumPost',
            'contentHtml' => null,
            'deleted' => true,
        ]);

        $client->request('DELETE', '/forum_posts/20');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/forum_posts/20');
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumPost',
            '@type' => 'ForumPost',
            'contentHtml' => null,
            'deleted' => true,
        ]);
    }

    public function testAlreadyDeletedHidden(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $client->request('GET', '/forum_posts/15');
        self::assertResponseStatusCodeSame(404);

        $client->request('DELETE', '/forum_posts/15');
        self::assertResponseIsSuccessful();
    }

    public function testUserPostsCount(): void
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
        self::assertNotNull($user);
        self::assertSame(7, $user->getForumPostsCount());

        // 2 - delete
        $client->request('DELETE', '/forum_posts/1');
        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        // 3 - after delete
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'claude.monet@free-work.fr',
        ]);
        self::assertNotNull($user);
        self::assertSame(6, $user->getForumPostsCount());
    }
}
