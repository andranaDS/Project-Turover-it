<?php

namespace App\Forum\Tests\Functional\ForumTopic;

use App\Forum\Entity\ForumTopic;
use App\Forum\Entity\ForumTopicFavorite;
use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ForumTopicFavoriteTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('PATCH', '/forum_topics/category-1-1-topic-1/favorite');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('PATCH', '/forum_topics/category-1-1-topic-1/favorite');

        self::assertResponseIsSuccessful();
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('PATCH', '/forum_topics/category-1-1-topic-1/favorite');

        self::assertResponseIsSuccessful();
    }

    public function testDeleteFavorite(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();
        $topic = $em->find(ForumTopic::class, 1);
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'claude.monet@free-work.fr',
        ]);
        self::assertNotNull($topic);
        self::assertNotNull($user);

        // 1 - before
        $topicFavorite = $em->getRepository(ForumTopicFavorite::class)->findOneBy([
            'user' => $user,
            'topic' => $topic,
        ]);
        self::assertNotNull($topicFavorite);

        // 2 - remove to favorites
        $client->request('PATCH', '/forum_topics/category-1-1-topic-1/favorite');
        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        // 3 - after
        $topicFavorite = $em->getRepository(ForumTopicFavorite::class)->findOneBy([
            'user' => $user,
            'topic' => $topic,
        ]);
        self::assertNull($topicFavorite);
    }

    public function testAddFavorite(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();
        $topic = $em->find(ForumTopic::class, 2);
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'claude.monet@free-work.fr',
        ]);
        self::assertNotNull($topic);
        self::assertNotNull($user);

        // 1 - before
        $topicFavorite = $em->getRepository(ForumTopicFavorite::class)->findOneBy([
            'user' => $user,
            'topic' => $topic,
        ]);
        self::assertNull($topicFavorite);

        // 2 - add to favorites
        $client->request('PATCH', '/forum_topics/category-1-1-topic-2/favorite');
        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        // 3 - after
        $topicFavorite = $em->getRepository(ForumTopicFavorite::class)->findOneBy([
            'user' => $user,
            'topic' => $topic,
        ]);
        self::assertNotNull($topicFavorite);
    }
}
