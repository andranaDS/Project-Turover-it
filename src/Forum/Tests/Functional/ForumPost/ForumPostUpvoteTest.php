<?php

namespace App\Forum\Tests\Functional\ForumPost;

use App\Forum\Entity\ForumTopicData;
use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ForumPostUpvoteTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('PATCH', '/forum_posts/1/upvote');

        self::assertResponseStatusCodeSame(401);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('PATCH', '/forum_posts/1/upvote');

        self::assertResponseIsSuccessful();
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('PATCH', '/forum_posts/1/upvote');

        self::assertResponseIsSuccessful();
    }

    public function testDownvote(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $client->request('GET', '/forum_posts/2');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumPost',
            '@id' => '/forum_posts/2',
            '@type' => 'ForumPost',
            'upvotesCount' => 2,
        ]);

        $client->request('PATCH', '/forum_posts/2/upvote');

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testUpvote(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $client->request('GET', '/forum_posts/3');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumPost',
            '@id' => '/forum_posts/3',
            '@type' => 'ForumPost',
            'upvotesCount' => 0,
        ]);

        $client->request('PATCH', '/forum_posts/3/upvote');

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        self::assertEmailCount(1);

        $userNotificationForumPostLikeEmail = self::getMailerMessage();
        self::assertNotNull($userNotificationForumPostLikeEmail);
        self::assertEmailHeaderSame($userNotificationForumPostLikeEmail, 'from', 'Free-Work <forum@free-work.com>');
        self::assertEmailHeaderSame($userNotificationForumPostLikeEmail, 'to', 'vincent.van-gogh@free-work.fr');
        self::assertEmailHeaderSame($userNotificationForumPostLikeEmail, 'subject', 'TEST: Un Free-worker vous remercie pour votre contribution');
        self::assertEmailHeaderSame($userNotificationForumPostLikeEmail, 'X-Mailjet-Campaign', 'user_notification_forum_post_like');
        self::assertEmailTextBodyContains($userNotificationForumPostLikeEmail, 'Une de vos contributions vient d’être likée par un Free-worker concernant le sujet');
        self::assertEmailTextBodyContains($userNotificationForumPostLikeEmail, 'Voir le post');
    }

    public function testUserForumPostUpvotesCount(): void
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
        self::assertSame(2, $user->getForumPostUpvotesCount());

        // 2 - upvote
        $client->request('PATCH', '/forum_posts/3/upvote');
        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        // 3 - after upvote
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'claude.monet@free-work.fr',
        ]);
        self::assertNotNull($user);
        self::assertSame(3, $user->getForumPostUpvotesCount());

        // 4 - downvote
        $client->request('PATCH', '/forum_posts/2/upvote');
        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        // 5 - after downvote
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'claude.monet@free-work.fr',
        ]);
        self::assertNotNull($user);
        self::assertSame(2, $user->getForumPostUpvotesCount());
    }

    public function testForumTopicUpvotesCount(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();

        // 1 - before
        $topicData = $em->getRepository(ForumTopicData::class)->findOneById(1);
        self::assertNotNull($topicData);
        self::assertSame(2, $topicData->getUpvotesCount());

        // 2 - upvote
        $client->request('PATCH', '/forum_posts/1/upvote');
        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        // 3 - after upvote
        $topicData = $em->getRepository(ForumTopicData::class)->findOneById(1);
        self::assertNotNull($topicData);
        self::assertSame(3, $topicData->getUpvotesCount());

        // 4 - downvote
        $client->request('PATCH', '/forum_posts/1/upvote');
        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        // 5 - after downvote
        $topicData = $em->getRepository(ForumTopicData::class)->findOneById(1);
        self::assertNotNull($topicData);
        self::assertSame(2, $topicData->getUpvotesCount());
    }
}
