<?php

namespace App\Blog\Tests\Functional\BlogPost;

use App\Blog\Entity\BlogPost;
use App\Blog\Entity\BlogPostData;
use App\Blog\Entity\BlogPostTrace;
use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;

class BlogPostTracePostTest extends ApiTestCase
{
    public function testLogged(): void
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
        $postData = $em->find(BlogPostData::class, 3);
        self::assertNotNull($postData);
        self::assertSame(0, $postData->getViewsCount());
        self::assertSame(0, $postData->getRecentViewsCount());

        // 1.2
        self::assertSame(8, $em->getRepository(BlogPostTrace::class)->count([]));

        // 2 - add a BlogPostTrace on a BlogPost
        $client->request('POST', '/blog_posts/blog-category-1-post-3-lorem/trace');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        // 3 - after

        // 3.1
        $postData = $em->find(BlogPostData::class, 3);
        self::assertNotNull($postData);
        self::assertSame(1, $postData->getViewsCount());
        self::assertSame(1, $postData->getRecentViewsCount());

        // 3.2
        self::assertSame(9, $em->getRepository(BlogPostTrace::class)->count([]));

        // 3.3
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => $email,
        ]);
        $lastPostTrace = $em->getRepository(BlogPostTrace::class)->findOneBy([], ['id' => Criteria::DESC]);
        self::assertNotNull($lastPostTrace);
        self::assertNotNull($user);
        self::assertSame($user, $lastPostTrace->getUser());
    }

    public function testNotLogged(): void
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
        $postData = $em->find(BlogPostData::class, 3);
        self::assertNotNull($postData);
        self::assertSame(0, $postData->getViewsCount());

        // 1.2
        self::assertSame(8, $em->getRepository(BlogPostTrace::class)->count([]));

        // 2 - add a BlogPostTrace on a BlogPost
        $client->request('POST', '/blog_posts/blog-category-1-post-3-lorem/trace');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        // 3 - after

        // 3.1
        $postData = $em->find(BlogPostData::class, 3);
        self::assertNotNull($postData);
        self::assertSame(1, $postData->getViewsCount());

        // 3.2
        self::assertSame(9, $em->getRepository(BlogPostTrace::class)->count([]));

        // 3.3
        $lastPostTrace = $em->getRepository(BlogPostTrace::class)->findOneBy([], ['id' => Criteria::DESC]);
        self::assertNotNull($lastPostTrace);
        self::assertNull($lastPostTrace->getUser());
    }

    public function testWithInvalidPostId(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('POST', '/blog_posts/blog-category-1-post-1337/trace');

        self::assertResponseStatusCodeSame(404);
    }
}
