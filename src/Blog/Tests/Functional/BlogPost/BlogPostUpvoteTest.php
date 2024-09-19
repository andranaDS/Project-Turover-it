<?php

namespace App\Blog\Tests\Functional\BlogPost;

use App\Blog\Entity\BlogPostData;
use App\Tests\Functional\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class BlogPostUpvoteTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('PATCH', '/blog_posts/blog-category-1-post-1/upvote');

        self::assertResponseStatusCodeSame(401);
    }

    public function testDownvote(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $client->request('GET', '/blog_posts/blog-category-1-post-2');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/BlogPost',
            '@id' => '/blog_posts/blog-category-1-post-2',
            '@type' => 'BlogPost',
        ]);

        $client->request('PATCH', '/blog_posts/blog-category-1-post-2/upvote');

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testUpvote(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $client->request('GET', '/blog_posts/blog-category-1-post-3-lorem');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/BlogPost',
            '@id' => '/blog_posts/blog-category-1-post-3-lorem',
            '@type' => 'BlogPost',
        ]);

        $client->request('PATCH', '/blog_posts/blog-category-1-post-3-lorem/upvote');

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testBlogTopicUpvotesCount(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();

        // 1 - before
        $postData = $em->getRepository(BlogPostData::class)->findOneById(1);
        self::assertNotNull($postData);
        self::assertSame(3, $postData->getUpvotesCount());

        // 2 - upvote
        $client->request('PATCH', '/blog_posts/blog-category-1-post-1/upvote');
        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        // 3 - after upvote
        $postData = $em->getRepository(BlogPostData::class)->findOneById(1);
        self::assertNotNull($postData);
        self::assertSame(4, $postData->getUpvotesCount());

        // 4 - downvote
        $client->request('PATCH', '/blog_posts/blog-category-1-post-1/upvote');
        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        // 5 - after downvote
        $postData = $em->getRepository(BlogPostData::class)->findOneById(1);
        self::assertNotNull($postData);
        self::assertSame(3, $postData->getUpvotesCount());
    }
}
