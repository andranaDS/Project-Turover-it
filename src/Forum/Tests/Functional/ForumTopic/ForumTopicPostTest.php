<?php

namespace App\Forum\Tests\Functional\ForumTopic;

use App\Forum\Entity\ForumTopic;
use App\Forum\Entity\ForumTopicData;
use App\Forum\Entity\ForumTopicTrace;
use App\Tests\Functional\ApiTestCase;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ForumTopicPostTest extends ApiTestCase
{
    public function testNotLogged(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('POST', '/forum_topics', [
            'json' => [
                'title' => 'Category 1.1 - Topic 4',
                'category' => '/forum_categories/category-1-1',
                'posts' => [
                    [
                        'contentHtml' => '<p>Category 1.1 - Topic 4- Post 1</p>',
                        'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 4- Post 1"}]}]}',
                    ],
                ],
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
    }

    public function testWithInvalidData(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient();
        $client->getContainer();

        $client->request('POST', '/forum_topics', [
            'json' => [
                'title' => '',
                'category' => '/forum_categories/category-1-1',
                'posts' => [],
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
                    'propertyPath' => 'title',
                    'message' => 'Cette valeur ne doit pas être vide.',
                ],
                [
                    'propertyPath' => 'posts',
                    'message' => 'Ce champ doit contenir 1 élément ou plus.',
                ],
            ],
        ]);
    }

    public function testWithValidData(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient(); // id 1

        $container = $client->getContainer();
        if (null === $container) {
            throw new \RuntimeException('Container is null');
        }

        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine')->getManager();

        // 1 - before - Topic doest exist
        /** @var User $user */
        $topic = $em->getRepository(ForumTopic::class)->findOneBySlug('category-1-1-topic-5');

        self::assertNull($topic);

        $client->request('POST', '/forum_topics', [
            'json' => [
                'title' => 'Category 1.1 - Topic 5',
                'category' => '/forum_categories/category-1-1',
                'posts' => [
                    [
                        'contentHtml' => '<p>Category 1.1 - Topic 5- Post 1</p>',
                        'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 5- Post 1"}]}]}',
                    ],
                ],
            ],
        ]);

        // check forum_topics data
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
            'title' => 'Category 1.1 - Topic 5',
            'slug' => 'category-1-1-topic-5',
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
                ],
                'contentHtml' => '<p>Category 1.1 - Topic 5- Post 1</p>',
            ],
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
                    'nickname' => 'User-Free-Work',
                    'nicknameSlug' => 'user-free-work',
                    'forumPostUpvotesCount' => 0,
                    'forumPostsCount' => 1,
                    'createdAt' => '2020-01-01T10:00:00+01:00',
                    'deleted' => false,
                ],
                'contentHtml' => '<p>Category 1.1 - Topic 5- Post 1</p>',
            ],
        ]);

        // check forum_categories data (lastPost, counts, ...)
        $client->request('GET', '/forum_categories/category-1-1');
        self::assertJsonContains([
            'topicsCount' => 5,
            'postsCount' => 12,
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
                ],
            ],
        ]);

        // 2 - after - Topic does exist and a Trace have been created and Data have been created
        $topic = $em->getRepository(ForumTopic::class)->findOneBySlug('category-1-1-topic-5');

        self::assertNotNull($topic);

        $trace = $em->getRepository(ForumTopicTrace::class)->findOneBy([
            'user' => $em->getRepository(User::class)->findOneById(1),
            'topicId' => $topic->getId(),
        ]);

        self::assertNotNull($trace);
        self::assertTrue($trace->getCreated());

        $data = $em->getRepository(ForumTopicData::class)->findOneById($topic->getId());
        self::assertNotNull($data);
    }

    public function testWithForbiddenContent(): void
    {
        $client = static::createFreeWorkAuthenticatedClient('claude.monet@free-work.fr');
        $client->request('POST', '/forum_topics', [
            'json' => [
                'title' => 'Category 1.1 - Topic 5 forbidden content 1, forbidden content 2 and forbidden content 3',
                'category' => '/forum_categories/category-1-1',
                'posts' => [
                    [
                        'contentHtml' => '<p>Category 1.1 - Topic 5- Post 1</p>',
                        'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 5- Post 1"}]}]}',
                    ],
                ],
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
                    'propertyPath' => 'title',
                    'message' => 'La valeur est constitué d\'élement(s) interdit: "forbidden content 1", "forbidden content 2", "forbidden content 3".',
                ],
            ],
        ]);
    }

    public function testWithTitleSanitizer(): void
    {
        $client = static::createFreeWorkAuthenticatedUserClient(); // id 1
        $client->request('POST', '/forum_topics', [
            'json' => [
                'title' => "Category 1.1 - Topic 6 sanitize <script>alert('alert')</script> special char ' @  & ; %",
                'category' => '/forum_categories/category-1-1',
                'posts' => [
                    [
                        'contentHtml' => '<p>Category 1.1 - Topic 6- Post 1</p>',
                        'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 4- Post 1"}]}]}',
                    ],
                ],
            ],
        ]);

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
            'title' => "Category 1.1 - Topic 6 sanitize  special char ' @  & ; %",
            'slug' => 'category-1-1-topic-6-sanitize-special-char',
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
                ],
                'contentHtml' => '<p>Category 1.1 - Topic 6- Post 1</p>',
            ],
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
                    'nickname' => 'User-Free-Work',
                    'nicknameSlug' => 'user-free-work',
                    'forumPostUpvotesCount' => 0,
                    'forumPostsCount' => 1,
                    'createdAt' => '2020-01-01T10:00:00+01:00',
                    'deleted' => false,
                ],
                'contentHtml' => '<p>Category 1.1 - Topic 6- Post 1</p>',
            ],
        ]);

        $client->request('POST', '/forum_topics', [
            'json' => [
                'title' => '<script>alert("alert")</script>',
                'category' => '/forum_categories/category-1-1',
                'posts' => [
                    [
                        'contentHtml' => '<p>Category 1.1 - Topic 6- Post 1</p>',
                        'contentJson' => '{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text","text":"Category 1.1 - Topic 4- Post 1"}]}]}',
                    ],
                ],
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
                    'propertyPath' => 'title',
                    'message' => 'Cette valeur ne doit pas être vide.',
                ],
            ],
        ]);
    }
}
