<?php

namespace App\Forum\Tests\Functional\ForumCategory;

use App\Forum\Entity\ForumCategory;
use App\Forum\Entity\ForumTopicData;
use App\Forum\Entity\ForumTopicTrace;
use App\Tests\Functional\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;

class ForumCategoryTraceTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('POST', '/forum_categories/category-1-1/trace');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('POST', '/forum_categories/category-1-1/trace');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('POST', '/forum_categories/category-1-1/trace');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testUserData(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $response = $client->request('GET', '/users/6/data?scopes=forum_topic_traces');
        self::assertResponseIsSuccessful();
        $topicIds = array_keys($response->toArray()['forum_topic_traces'] ?? []);
        self::assertSame([1, 2, 4, 5, 6, 7, 8], $topicIds);

        $client->request('POST', '/forum_categories/category-1-1/trace');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumCategory',
            '@type' => 'ForumCategory',
            'title' => 'Category 1.1',
        ]);

        $response = $client->request('GET', '/users/6/data?scopes=forum_topic_traces');
        self::assertResponseIsSuccessful();
        $topicIds = array_keys($response->toArray()['forum_topic_traces'] ?? []);
        self::assertSame([1, 2, 3, 4, 5, 6, 7, 8, 9], $topicIds);
    }

    public function testViewsCount(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();

        // before
        $category = $em->find(ForumCategory::class, 2);
        self::assertNotNull($category);
        self::assertSame([
            ['id' => 1, 'viewsCount' => 4],
            ['id' => 2, 'viewsCount' => 1],
            ['id' => 3, 'viewsCount' => 0],
            ['id' => 9, 'viewsCount' => 0],
        ], $em->getRepository(ForumTopicData::class)->getViewsCount($category));
        self::assertSame(9, $em->getRepository(ForumTopicTrace::class)->count(['created' => false]));

        // call
        $client->request('POST', '/forum_categories/category-1-1/trace');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumCategory',
            '@type' => 'ForumCategory',
            'title' => 'Category 1.1',
        ]);

        // after
        // same countViews but more traces
        $category = $em->find(ForumCategory::class, 2);
        self::assertNotNull($category);
        self::assertSame([
            ['id' => 1, 'viewsCount' => 4],
            ['id' => 2, 'viewsCount' => 1],
            ['id' => 3, 'viewsCount' => 0],
            ['id' => 9, 'viewsCount' => 0],
        ], $em->getRepository(ForumTopicData::class)->getViewsCount($category));
        self::assertSame(13, $em->getRepository(ForumTopicTrace::class)->count(['created' => false]));
    }

    public function testWithInvalidCategorySlug(): void
    {
        $client = static::createFreeWorkAuthenticatedClient();
        $client->request('POST', '/forum_categories/wrong-slug/trace');

        self::assertResponseStatusCodeSame(404);
    }
}
