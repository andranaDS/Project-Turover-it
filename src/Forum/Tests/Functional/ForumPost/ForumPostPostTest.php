<?php

namespace App\Forum\Tests\Functional\ForumPost;

use App\Forum\Entity\ForumTopic;
use App\Forum\Entity\ForumTopicTrace;
use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ForumPostPostTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('POST', '/forum_posts', [
            'json' => [
                'topic' => '/forum_topics/category-1-1-topic-1',
                'parent' => null,
                'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 3</p>',
                'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 1 - Post 3"}]}]}',
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testBanned(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('user-ban@free-work.fr');
        $client->request('POST', '/forum_posts', [
            'json' => [
                'topic' => '/forum_topics/category-1-1-topic-1',
                'parent' => null,
                'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 3</p>',
                'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 1 - Post 3"}]}]}',
            ],
        ]);

        self::assertResponseStatusCodeSame(403);
    }

    public function testLoggedAsUser(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('POST', '/forum_posts', [
            'json' => [
                'topic' => '/forum_topics/category-1-1-topic-1',
                'parent' => null,
                'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 3</p>',
                'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 1 - Post 3"}]}]}',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testLoggedAsAdmin(): void
    {
        $client = static::createFreeWorkAuthenticatedAdminClient();
        $client->request('POST', '/forum_posts', [
            'json' => [
                'topic' => '/forum_topics/category-1-1-topic-1',
                'parent' => null,
                'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 3</p>',
                'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 1 - Post 3"}]}]}',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    public function testWithValidDataWithoutParent(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('POST', '/forum_posts', [
            'json' => [
                'topic' => '/forum_topics/category-1-1-topic-1',
                'parent' => null,
                'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 3</p>',
                'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 1 - Post 3"}]}]}',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumPost',
            '@type' => 'ForumPost',
            'topic' => [
                '@id' => '/forum_topics/category-1-1-topic-1',
                '@type' => 'ForumTopic',
                'id' => 1,
                'category' => [
                    '@id' => '/forum_categories/category-1-1',
                    '@type' => 'ForumCategory',
                    'id' => 2,
                    'title' => 'Category 1.1',
                    'slug' => 'category-1-1',
                ],
                'title' => 'Category 1.1 - Topic 1',
                'slug' => 'category-1-1-topic-1',
            ],
            'parent' => null,
            'author' => [
                '@type' => 'User',
                'nickname' => 'User-Free-Work',
                'nicknameSlug' => 'user-free-work',
                'jobTitle' => null,
                'website' => null,
                'signature' => null,
                'avatar' => null,
                'displayAvatar' => false,
                'forumPostUpvotesCount' => 0,
                'forumPostsCount' => 1,
                'createdAt' => '2020-01-01T10:00:00+01:00',
                'deleted' => false,
                'admin' => false,
            ],
            'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 3</p>',
            'upvotesCount' => 0,
        ]);

        self::assertEmailCount(1);

        $userNotificationForumTopicFavoriteEmail = self::getMailerMessage();
        self::assertNotNull($userNotificationForumTopicFavoriteEmail);
        self::assertEmailHeaderSame($userNotificationForumTopicFavoriteEmail, 'from', 'Free-Work <forum@free-work.com>');
        self::assertEmailHeaderSame($userNotificationForumTopicFavoriteEmail, 'to', 'claude.monet@free-work.fr');
        self::assertEmailHeaderSame($userNotificationForumTopicFavoriteEmail, 'subject', 'TEST: Nouveau post dans le sujet «Category 1.1 - Topic 1»');
        self::assertEmailHeaderSame($userNotificationForumTopicFavoriteEmail, 'X-Mailjet-Campaign', 'user_notification_forum_topic_favorite');
        self::assertEmailTextBodyContains($userNotificationForumTopicFavoriteEmail, 'Le sujet que vous avez mis en favori «Category 1.1 - Topic 1» sur le forum vient de recevoir une réponse d’un Free-worker.');
        self::assertEmailTextBodyContains($userNotificationForumTopicFavoriteEmail, 'Voir les nouveaux posts');

        $client->request('GET', '/forum_topics/category-1-1-topic-1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumTopic',
            '@type' => 'ForumTopic',
            'category' => [
                '@type' => 'ForumCategory',
                'title' => 'Category 1.1',
                'slug' => 'category-1-1',
            ],
            'title' => 'Category 1.1 - Topic 1',
            'slug' => 'category-1-1-topic-1',
            'pinned' => false,
            'lastPost' => [
                '@type' => 'ForumPost',
                'author' => [
                    '@type' => 'User',
                    'jobTitle' => null,
                    'website' => null,
                    'signature' => null,
                    'avatar' => null,
                    'displayAvatar' => false,
                    'nickname' => 'User-Free-Work',
                    'nicknameSlug' => 'user-free-work',
                    'forumPostUpvotesCount' => 0,
                    'forumPostsCount' => 1,
                    'createdAt' => '2020-01-01T10:00:00+01:00',
                    'deleted' => false,
                    'admin' => false,
                ],
                'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 3</p>',
            ],
            'author' => [
                '@type' => 'User',
                'jobTitle' => 'Peintre',
                'website' => 'https://fr.wikipedia.org/wiki/Claude_Monet',
                'signature' => 'Claude Monet.',
                'avatar' => [
                    'xSmall' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_x_small/monet-avatar.jpg',
                    'small' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_small/monet-avatar.jpg',
                    'medium' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_medium/monet-avatar.jpg',
                ],
                'displayAvatar' => true,
                'nickname' => 'Claude-Monet',
                'nicknameSlug' => 'claude-monet',
                'forumPostUpvotesCount' => 2,
                'forumPostsCount' => 7,
                'createdAt' => '2020-01-01T10:00:00+01:00',
                'deleted' => false,
            ],
            'initialPost' => [
                '@type' => 'ForumPost',
                'author' => [
                    '@type' => 'User',
                    'jobTitle' => 'Peintre',
                    'website' => 'https://fr.wikipedia.org/wiki/Claude_Monet',
                    'signature' => 'Claude Monet.',
                    'avatar' => [
                        'xSmall' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_x_small/monet-avatar.jpg',
                        'small' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_small/monet-avatar.jpg',
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_medium/monet-avatar.jpg',
                    ],
                    'displayAvatar' => true,
                    'nickname' => 'Claude-Monet',
                    'nicknameSlug' => 'claude-monet',
                    'forumPostUpvotesCount' => 2,
                    'forumPostsCount' => 7,
                    'createdAt' => '2020-01-01T10:00:00+01:00',
                    'deleted' => false,
                    'admin' => false,
                ],
                'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 1 - Lorem</p>',
            ],
        ]);

        // test userNotificationForumTopicReplyEmail (without topic being also favorite)
        $client->request('POST', '/forum_posts', [
            'json' => [
                'topic' => '/forum_topics/category-1-2-topic-1', // topic_id = 4
                'parent' => null,
                'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 3</p>',
                'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 1 - Post 3"}]}]}',
            ],
        ]);

        self::assertEmailCount(1);

        $userNotificationForumTopicReplyEmail = self::getMailerMessage();
        self::assertNotNull($userNotificationForumTopicReplyEmail);
        self::assertEmailHeaderSame($userNotificationForumTopicReplyEmail, 'from', 'Free-Work <forum@free-work.com>');
        self::assertEmailHeaderSame($userNotificationForumTopicReplyEmail, 'to', 'claude.monet@free-work.fr');
        self::assertEmailHeaderSame($userNotificationForumTopicReplyEmail, 'subject', 'TEST: Nouveau post dans le sujet «Category 1.2 - Topic 1»');
        self::assertEmailHeaderSame($userNotificationForumTopicReplyEmail, 'X-Mailjet-Campaign', 'user_notification_forum_topic_reply');
        self::assertEmailTextBodyContains($userNotificationForumTopicReplyEmail, 'Le sujet que vous avez créé «Category 1.2 - Topic 1» sur le forum vient de recevoir une réponse d’un Free-worker.');
        self::assertEmailTextBodyContains($userNotificationForumTopicReplyEmail, 'Voir les nouveaux posts');
    }

    public function testWithValidDataWithParentFirstLevel(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('POST', '/forum_posts', [
            'json' => [
                'topic' => '/forum_topics/category-1-1-topic-1',
                'parent' => '/forum_posts/2',
                'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 2.1</p>',
                'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 1 - Post 2.1"}]}]}',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumPost',
            '@type' => 'ForumPost',
            'topic' => [
                '@id' => '/forum_topics/category-1-1-topic-1',
                '@type' => 'ForumTopic',
                'id' => 1,
                'category' => [
                    '@id' => '/forum_categories/category-1-1',
                    '@type' => 'ForumCategory',
                    'id' => 2,
                    'title' => 'Category 1.1',
                    'slug' => 'category-1-1',
                ],
                'title' => 'Category 1.1 - Topic 1',
                'slug' => 'category-1-1-topic-1',
            ],
            'parent' => [
                '@type' => 'ForumPost',
                'author' => [
                    '@type' => 'User',
                    'nickname' => 'Vincent-van-Gogh',
                    'nicknameSlug' => 'vincent-van-gogh',
                    'jobTitle' => null,
                    'website' => null,
                    'signature' => null,
                    'avatar' => null,
                    'displayAvatar' => false,
                    'forumPostUpvotesCount' => 0,
                    'forumPostsCount' => 6,
                    'createdAt' => '2020-01-01T10:00:00+01:00',
                    'deleted' => false,
                    'admin' => false,
                ],
                'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 2</p>',
                'upvotesCount' => 2,
                'createdAt' => '2021-01-01T13:00:00+01:00',
                'updatedAt' => '2021-01-01T13:00:00+01:00',
            ],
            'author' => [
                '@type' => 'User',
                'nickname' => 'User-Free-Work',
                'nicknameSlug' => 'user-free-work',
                'jobTitle' => null,
                'website' => null,
                'signature' => null,
                'avatar' => null,
                'displayAvatar' => false,
                'forumPostUpvotesCount' => 0,
                'forumPostsCount' => 1,
                'createdAt' => '2020-01-01T10:00:00+01:00',
                'deleted' => false,
                'admin' => false,
            ],
            'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 2.1</p>',
            'upvotesCount' => 0,
        ]);

        self::assertEmailCount(2);

        $userNotificationForumTopicFavoriteEmail = self::getMailerMessage();
        self::assertNotNull($userNotificationForumTopicFavoriteEmail);
        self::assertEmailHeaderSame($userNotificationForumTopicFavoriteEmail, 'from', 'Free-Work <forum@free-work.com>');
        self::assertEmailHeaderSame($userNotificationForumTopicFavoriteEmail, 'to', 'claude.monet@free-work.fr');
        self::assertEmailHeaderSame($userNotificationForumTopicFavoriteEmail, 'subject', 'TEST: Nouveau post dans le sujet «Category 1.1 - Topic 1»');
        self::assertEmailHeaderSame($userNotificationForumTopicFavoriteEmail, 'X-Mailjet-Campaign', 'user_notification_forum_topic_favorite');
        self::assertEmailTextBodyContains($userNotificationForumTopicFavoriteEmail, 'Le sujet que vous avez mis en favori «Category 1.1 - Topic 1» sur le forum vient de recevoir une réponse d’un Free-worker.');
        self::assertEmailTextBodyContains($userNotificationForumTopicFavoriteEmail, 'Voir les nouveaux posts');

        $userNotificationForumPostReplyEmail = self::getMailerMessage(2);
        self::assertNotNull($userNotificationForumPostReplyEmail);
        self::assertEmailHeaderSame($userNotificationForumPostReplyEmail, 'from', 'Free-Work <forum@free-work.com>');
        self::assertEmailHeaderSame($userNotificationForumPostReplyEmail, 'to', 'vincent.van-gogh@free-work.fr');
        self::assertEmailHeaderSame($userNotificationForumPostReplyEmail, 'subject', 'TEST: Nouveau commentaire suite à votre post');
        self::assertEmailHeaderSame($userNotificationForumPostReplyEmail, 'X-Mailjet-Campaign', 'user_notification_forum_post_reply');
        self::assertEmailTextBodyContains($userNotificationForumPostReplyEmail, 'Un Free-worker vient de commenter votre post à propos du sujet «Category 1.1 - Topic 1».');
        self::assertEmailTextBodyContains($userNotificationForumPostReplyEmail, 'Voir le sujet');

        $client->request('GET', '/forum_topics/category-1-1-topic-1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumTopic',
            '@type' => 'ForumTopic',
            'category' => [
                '@type' => 'ForumCategory',
                'title' => 'Category 1.1',
                'slug' => 'category-1-1',
            ],
            'title' => 'Category 1.1 - Topic 1',
            'slug' => 'category-1-1-topic-1',
            'pinned' => false,
            'lastPost' => [
                '@type' => 'ForumPost',
                'author' => [
                    '@type' => 'User',
                    'jobTitle' => null,
                    'website' => null,
                    'signature' => null,
                    'avatar' => null,
                    'displayAvatar' => false,
                    'nickname' => 'User-Free-Work',
                    'nicknameSlug' => 'user-free-work',
                    'forumPostUpvotesCount' => 0,
                    'forumPostsCount' => 1,
                    'createdAt' => '2020-01-01T10:00:00+01:00',
                    'deleted' => false,
                    'admin' => false,
                ],
                'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 2.1</p>',
            ],
            'author' => [
                '@type' => 'User',
                'jobTitle' => 'Peintre',
                'website' => 'https://fr.wikipedia.org/wiki/Claude_Monet',
                'avatar' => [
                    'xSmall' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_x_small/monet-avatar.jpg',
                    'small' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_small/monet-avatar.jpg',
                    'medium' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_medium/monet-avatar.jpg',
                ],

                'displayAvatar' => true,
                'nickname' => 'Claude-Monet',
                'nicknameSlug' => 'claude-monet',
                'forumPostUpvotesCount' => 2,
                'forumPostsCount' => 7,
                'createdAt' => '2020-01-01T10:00:00+01:00',
                'deleted' => false,
                'admin' => false,
            ],
            'initialPost' => [
                '@type' => 'ForumPost',
                'author' => [
                    '@type' => 'User',
                    'jobTitle' => 'Peintre',
                    'website' => 'https://fr.wikipedia.org/wiki/Claude_Monet',
                    'signature' => 'Claude Monet.',
                    'avatar' => [
                        'xSmall' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_x_small/monet-avatar.jpg',
                        'small' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_small/monet-avatar.jpg',
                        'medium' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_medium/monet-avatar.jpg',
                    ],
                    'displayAvatar' => true,
                    'nickname' => 'Claude-Monet',
                    'nicknameSlug' => 'claude-monet',
                    'forumPostUpvotesCount' => 2,
                    'forumPostsCount' => 7,
                    'createdAt' => '2020-01-01T10:00:00+01:00',
                    'deleted' => false,
                    'admin' => false,
                ],
                'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 1 - Lorem</p>',
            ],
        ]);
    }

    public function testWithValidDataWithParentMiddleLevel(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('POST', '/forum_posts', [
            'json' => [
                'topic' => '/forum_topics/category-1-1-topic-2',
                'parent' => '/forum_posts/4',
                'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.4</p>',
                'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 2 - Post 2.4"}]}]}',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumPost',
            '@type' => 'ForumPost',
            'topic' => [
                '@id' => '/forum_topics/category-1-1-topic-2',
                '@type' => 'ForumTopic',
                'id' => 2,
                'category' => [
                    '@id' => '/forum_categories/category-1-1',
                    '@type' => 'ForumCategory',
                    'id' => 2,
                    'title' => 'Category 1.1',
                    'slug' => 'category-1-1',
                ],
                'title' => 'Category 1.1 - Topic 2',
                'slug' => 'category-1-1-topic-2',
            ],
            'parent' => [
                '@type' => 'ForumPost',
                'author' => [
                    '@type' => 'User',
                    'nickname' => 'Auguste-Renoir',
                    'nicknameSlug' => 'auguste-renoir',
                    'jobTitle' => null,
                    'website' => null,
                    'signature' => null,
                    'avatar' => null,
                    'displayAvatar' => false,
                    'forumPostUpvotesCount' => 0,
                    'forumPostsCount' => 3,
                    'createdAt' => '2020-01-01T10:00:00+01:00',
                    'deleted' => false,
                    'admin' => false,
                ],
                'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2 - Lorem</p>',
                'upvotesCount' => 0,
            ],
            'author' => [
                '@type' => 'User',
                'nickname' => 'User-Free-Work',
                'nicknameSlug' => 'user-free-work',
                'jobTitle' => null,
                'website' => null,
                'signature' => null,
                'avatar' => null,
                'displayAvatar' => false,
                'forumPostUpvotesCount' => 0,
                'forumPostsCount' => 1,
                'createdAt' => '2020-01-01T10:00:00+01:00',
                'deleted' => false,
                'admin' => false,
            ],
            'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.4</p>',
            'upvotesCount' => 0,
        ]);

        $client = static::createFreeWorkClient();
        $client->request('GET', '/forum_topics/category-1-1-topic-2');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumTopic',
            '@type' => 'ForumTopic',
            'category' => [
                '@type' => 'ForumCategory',
                'title' => 'Category 1.1',
                'slug' => 'category-1-1',
            ],
            'title' => 'Category 1.1 - Topic 2',
            'slug' => 'category-1-1-topic-2',
            'pinned' => false,
            'lastPost' => [
                '@type' => 'ForumPost',
                'author' => [
                    '@type' => 'User',
                    'jobTitle' => null,
                    'website' => null,
                    'signature' => null,
                    'avatar' => null,
                    'displayAvatar' => false,
                    'nickname' => 'User-Free-Work',
                    'nicknameSlug' => 'user-free-work',
                    'forumPostUpvotesCount' => 0,
                    'forumPostsCount' => 1,
                    'createdAt' => '2020-01-01T10:00:00+01:00',
                    'deleted' => false,
                    'admin' => false,
                ],
                'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.4</p>',
            ],
            'author' => [
                '@type' => 'User',
                'jobTitle' => null,
                'website' => null,
                'signature' => null,
                'avatar' => null,
                'displayAvatar' => false,
                'nickname' => 'Vincent-van-Gogh',
                'nicknameSlug' => 'vincent-van-gogh',
                'forumPostUpvotesCount' => 0,
                'forumPostsCount' => 6,
                'createdAt' => '2020-01-01T10:00:00+01:00',
                'deleted' => false,
                'admin' => false,
            ],
            'initialPost' => [
                '@type' => 'ForumPost',
                'author' => [
                    '@type' => 'User',
                    'jobTitle' => null,
                    'website' => null,
                    'signature' => null,
                    'avatar' => null,
                    'displayAvatar' => false,
                    'nickname' => 'Vincent-van-Gogh',
                    'nicknameSlug' => 'vincent-van-gogh',
                    'forumPostUpvotesCount' => 0,
                    'forumPostsCount' => 6,
                    'createdAt' => '2020-01-01T10:00:00+01:00',
                    'deleted' => false,
                    'admin' => false,
                ],
                'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 1</p>',
                'createdAt' => '2021-01-10T19:00:00+01:00',
                'updatedAt' => '2021-01-10T19:00:00+01:00',
            ],
        ]);
    }

    public function testWithValidDataWithParentLastLevel(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('POST', '/forum_posts', [
            'json' => [
                'topic' => '/forum_topics/category-1-1-topic-2',
                'parent' => '/forum_posts/6',
                'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.2.2</p>',
                'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 2 - Post 2.2.2"}]}]}',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumPost',
            '@type' => 'ForumPost',
            'topic' => [
                '@id' => '/forum_topics/category-1-1-topic-2',
                '@type' => 'ForumTopic',
                'id' => 2,
                'category' => [
                    '@id' => '/forum_categories/category-1-1',
                    '@type' => 'ForumCategory',
                    'id' => 2,
                    'title' => 'Category 1.1',
                    'slug' => 'category-1-1',
                ],
                'title' => 'Category 1.1 - Topic 2',
                'slug' => 'category-1-1-topic-2',
            ],

            'parent' => [
                '@type' => 'ForumPost',
                'author' => [
                    '@type' => 'User',
                    'nickname' => 'Auguste-Renoir',
                    'nicknameSlug' => 'auguste-renoir',
                    'jobTitle' => null,
                    'website' => null,
                    'signature' => null,
                    'avatar' => null,
                    'displayAvatar' => false,
                    'forumPostUpvotesCount' => 0,
                    'forumPostsCount' => 3,
                    'createdAt' => '2020-01-01T10:00:00+01:00',
                    'deleted' => false,
                    'admin' => false,
                ],
                'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.2</p>',
                'upvotesCount' => 0,
            ],
            'author' => [
                '@type' => 'User',
                'nickname' => 'User-Free-Work',
                'nicknameSlug' => 'user-free-work',
                'jobTitle' => null,
                'website' => null,
                'signature' => null,
                'avatar' => null,
                'displayAvatar' => false,
                'forumPostUpvotesCount' => 0,
                'forumPostsCount' => 1,
                'createdAt' => '2020-01-01T10:00:00+01:00',
                'deleted' => false,
                'admin' => false,
            ],
            'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.2.2</p>',
            'upvotesCount' => 0,
        ]);

        $client = static::createFreeWorkClient();
        $client->request('GET', '/forum_topics/category-1-1-topic-2');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumTopic',
            '@type' => 'ForumTopic',
            'category' => [
                '@type' => 'ForumCategory',
                'title' => 'Category 1.1',
                'slug' => 'category-1-1',
            ],
            'title' => 'Category 1.1 - Topic 2',
            'slug' => 'category-1-1-topic-2',
            'pinned' => false,
            'lastPost' => [
                '@type' => 'ForumPost',
                'author' => [
                    '@type' => 'User',
                    'jobTitle' => null,
                    'website' => null,
                    'signature' => null,
                    'avatar' => null,
                    'displayAvatar' => false,
                    'nickname' => 'User-Free-Work',
                    'nicknameSlug' => 'user-free-work',
                    'forumPostUpvotesCount' => 0,
                    'forumPostsCount' => 1,
                    'createdAt' => '2020-01-01T10:00:00+01:00',
                    'deleted' => false,
                    'admin' => false,
                ],
                'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 2.2.2</p>',
            ],
            'author' => [
                '@type' => 'User',
                'jobTitle' => null,
                'website' => null,
                'signature' => null,
                'avatar' => null,
                'displayAvatar' => false,
                'nickname' => 'Vincent-van-Gogh',
                'nicknameSlug' => 'vincent-van-gogh',
                'forumPostUpvotesCount' => 0,
                'forumPostsCount' => 6,
                'createdAt' => '2020-01-01T10:00:00+01:00',
                'deleted' => false,
                'admin' => false,
            ],
            'initialPost' => [
                '@type' => 'ForumPost',
                'author' => [
                    '@type' => 'User',
                    'jobTitle' => null,
                    'website' => null,
                    'signature' => null,
                    'avatar' => null,
                    'displayAvatar' => false,
                    'nickname' => 'Vincent-van-Gogh',
                    'nicknameSlug' => 'vincent-van-gogh',
                    'forumPostUpvotesCount' => 0,
                    'forumPostsCount' => 6,
                    'createdAt' => '2020-01-01T10:00:00+01:00',
                    'deleted' => false,
                    'admin' => false,
                ],
                'contentHtml' => '<p>Category 1.1 - Topic 2 - Post 1</p>',
                'createdAt' => '2021-01-10T19:00:00+01:00',
                'updatedAt' => '2021-01-10T19:00:00+01:00',
            ],
        ]);
    }

    public function testWithInvalidData(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('POST', '/forum_posts', [
            'json' => [
                'topic' => '/forum_topics/category-1-1-topic-1',
                'parent' => null,
                'contentHtml' => '',
                'contentJson' => '',
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'violations' => [
                [
                    'propertyPath' => 'contentHtml',
                    'message' => 'Cette valeur ne doit pas être vide.',
                ],
                [
                    'propertyPath' => 'contentJson',
                    'message' => 'Cette valeur ne doit pas être vide.',
                ],
            ],
        ]
        );
    }

    public function testWithMissingData(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->request('POST', '/forum_posts', [
            'json' => [
                'topic' => '/forum_topics/category-1-1-topic-1',
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'violations' => [
                [
                    'propertyPath' => 'contentHtml',
                    'message' => 'Cette valeur ne doit pas être vide.',
                ],
                [
                    'propertyPath' => 'contentJson',
                    'message' => 'Cette valeur ne doit pas être vide.',
                ],
            ],
        ]);
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

        // 2 - post
        $client->request('POST', '/forum_posts', [
            'json' => [
                'topic' => '/forum_topics/category-1-1-topic-1',
                'parent' => null,
                'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 4</p>',
                'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 1 - Post 4"}]}]}',
            ],
        ]);
        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        // 3 - after post
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'claude.monet@free-work.fr',
        ]);
        self::assertNotNull($user);
        self::assertSame(8, $user->getForumPostsCount());
    }

    public function testWithSensitiveContent(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('POST', '/forum_posts', [
            'json' => [
                'topic' => '/forum_topics/category-1-1-topic-1',
                'parent' => null,
                'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 3 sensitive content 1, sensitive content 2 and sensitive content 3</p>',
                'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 1 - Post 3 sensitive content 1, sensitive content 2 and sensitive content 3"}]}]}',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $email = self::getMailerMessage();
        self::assertNotNull($email);
        self::assertEmailHeaderSame($email, 'from', 'Free-Work <contact@free-work.com>');
        self::assertEmailHeaderSame($email, 'to', 'marketing@free-work.com');
        self::assertEmailHeaderSame($email, 'subject', 'TEST: Contenu sensible détecté');
        self::assertEmailTextBodyContains($email, 'ForumPost');
        self::assertEmailTextBodyContains($email, 'Category 1.1 - Topic 1 - Post 3 **sensitive content 1**, **sensitive content 2** and **sensitive content 3**');
    }

    public function testWithForbiddenContent(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('POST', '/forum_posts', [
            'json' => [
                'topic' => '/forum_topics/category-1-1-topic-1',
                'parent' => null,
                'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 3 forbidden content 1, forbidden content 2 and forbidden content 3</p>',
                'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 1 - Post 3 forbidden content 1, forbidden content 2 and forbidden content 3"}]}]}',
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'violations' => [
                [
                    'propertyPath' => 'contentHtml',
                    'message' => 'La valeur est constitué d\'élement(s) interdit: "forbidden content 1", "forbidden content 2", "forbidden content 3".',
                ],
            ],
        ]);
    }

    public function testWithContentSanitizer(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('POST', '/forum_posts', [
            'json' => [
                'topic' => '/forum_topics/category-1-1-topic-1',
                'parent' => null,
                'contentHtml' => '<iframe></iframe><p>Category 1.1 - Topic 1<script>alert("alert")</script></p><script>alert("alert")</script>',
                'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 1 - Post 3"}]}]}',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumPost',
            '@type' => 'ForumPost',
            'contentHtml' => '<p>Category 1.1 - Topic 1</p>',
        ]);

        $client->request('POST', '/forum_posts', [
            'json' => [
                'topic' => '/forum_topics/category-1-1-topic-1',
                'parent' => null,
                'contentHtml' => '<script>alert("alert")</script>',
                'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 1 - Post 3"}]}]}',
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'violations' => [
                [
                    'propertyPath' => 'contentHtml',
                    'message' => 'Cette valeur ne doit pas être vide.',
                ],
            ],
        ]);
    }

    public function testCreatedTrace(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('pablo.picasso@free-work.fr');

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();

        // 1 - before
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'pablo.picasso@free-work.fr',
        ]);
        self::assertNotNull($user);
        $topic = $em->getRepository(ForumTopic::class)->findOneBySlug('category-1-1-topic-1');
        self::assertNotNull($topic);
        $trace = $em->getRepository(ForumTopicTrace::class)->findOneBy([
            'user' => $user,
            'topicId' => $topic->getId(),
        ]);
        self::assertNull($trace);

        // 2 - post
        $client->request('POST', '/forum_posts', [
            'json' => [
                'topic' => '/forum_topics/category-1-1-topic-1',
                'parent' => null,
                'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 5</p>',
                'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 1 - Post 5"}]}]}',
            ],
        ]);
        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        // 3 - after post
        $user = $em->getRepository(User::class)->findOneBy([
            'email' => 'pablo.picasso@free-work.fr',
        ]);
        self::assertNotNull($user);
        $topic = $em->getRepository(ForumTopic::class)->findOneBySlug('category-1-1-topic-1');
        self::assertNotNull($topic);
        $trace = $em->getRepository(ForumTopicTrace::class)->findOneBy([
            'user' => $user,
            'topicId' => $topic->getId(),
        ]);
        self::assertNotNull($trace);
        self::assertTrue($trace->getCreated());
    }
}
