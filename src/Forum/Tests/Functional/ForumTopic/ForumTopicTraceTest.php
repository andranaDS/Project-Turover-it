<?php

namespace App\Forum\Tests\Functional\ForumTopic;

use App\Forum\Entity\ForumTopic;
use App\Forum\Entity\ForumTopicData;
use App\Forum\Entity\ForumTopicTrace;
use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;

class ForumTopicTraceTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('POST', '/forum_topics/category-1-1-topic-1/trace');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('POST', '/forum_topics/category-1-1-topic-1/trace');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('POST', '/forum_topics/category-1-1-topic-1/trace');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testAuthenticated(): void
    {
        $email = 'claude.monet@free-work.fr';
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();

        // 1 - before
        // 1.1
        $response = $client->request('GET', '/users/6/data?scopes=forum_topic_traces');
        self::assertResponseIsSuccessful();
        $topicIds = array_keys($response->toArray()['forum_topic_traces'] ?? []);
        self::assertSame([1, 2, 4, 5, 6, 7, 8], $topicIds);

        // 1.2
        $topicData = $em->find(ForumTopicData::class, 3);
        self::assertNotNull($topicData);
        self::assertSame(0, $topicData->getViewsCount());

        // 1.3
        self::assertSame(9, $em->getRepository(ForumTopicTrace::class)->count(['created' => false]));

        // 2 - add a ForumTopicTrace on a ForumTopic
        $client->request('POST', '/forum_topics/category-1-1-topic-3/trace');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        // 3 - after
        // 3.1
        $response = $client->request('GET', '/users/6/data?scopes=forum_topic_traces');
        self::assertResponseIsSuccessful();
        $topicIds = array_keys($response->toArray()['forum_topic_traces'] ?? []);
        self::assertSame([1, 2, 3, 4, 5, 6, 7, 8], $topicIds);

        // 3.2
        $topicData = $em->find(ForumTopicData::class, 3);
        self::assertNotNull($topicData);
        self::assertSame(1, $topicData->getViewsCount());

        // 3.3
        self::assertSame(10, $em->getRepository(ForumTopicTrace::class)->count(['created' => false]));

        // 3.4
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => $email,
        ]);
        $lastTopicTrace = $em->getRepository(ForumTopicTrace::class)->findOneBy([], ['id' => Criteria::DESC]);
        self::assertNotNull($lastTopicTrace);
        self::assertNotNull($user);
        self::assertSame($user, $lastTopicTrace->getUser());
    }

    public function testAnonymous(): void
    {
        $client = static::createFreeWorkClient();

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();

        // 1 - before
        // 1.1
        $topicData = $em->find(ForumTopicData::class, 3);
        self::assertNotNull($topicData);
        self::assertSame(0, $topicData->getViewsCount());

        // 1.2
        self::assertSame(9, $em->getRepository(ForumTopicTrace::class)->count(['created' => false]));

        // 2 - add a ForumTopicTrace on a ForumTopic
        $client->request('POST', '/forum_topics/category-1-1-topic-3/trace');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        // 3 - after
        // 3.1
        $topicData = $em->find(ForumTopicData::class, 3);
        self::assertNotNull($topicData);
        self::assertSame(1, $topicData->getViewsCount());

        // 3.1
        self::assertSame(10, $em->getRepository(ForumTopicTrace::class)->count(['created' => false]));

        // 3.2
        $lastTopicTrace = $em->getRepository(ForumTopicTrace::class)->findOneBy([], ['id' => Criteria::DESC]);
        self::assertNotNull($lastTopicTrace);
        self::assertNull($lastTopicTrace->getUser());
    }

    public function testWithInvalidTopicId(): void
    {
        $client = static::createFreeWorkAuthenticatedClient();
        $client->request('POST', '/forum_topics/category-1-1-topic-1337/trace');

        self::assertResponseStatusCodeSame(404);
    }
}
