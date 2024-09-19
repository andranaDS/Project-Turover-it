<?php

namespace App\Forum\Tests\Functional\ForumPost;

use App\Tests\Functional\ApiTestCase;

class ForumPostGetTest extends ApiTestCase
{
    public function testData(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/forum_posts/1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/ForumPost',
            '@id' => '/forum_posts/1',
            '@type' => 'ForumPost',
            'id' => 1,
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
            'children' => [],
            'author' => [
                '@id' => '/users/6',
                '@type' => 'User',
                'id' => 6,
                'nickname' => 'Claude-Monet',
                'nicknameSlug' => 'claude-monet',
                'jobTitle' => 'Peintre',
                'website' => 'https://fr.wikipedia.org/wiki/Claude_Monet',
                'signature' => 'Claude Monet.',
                'avatar' => [
                    'xSmall' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_x_small/monet-avatar.jpg',
                    'small' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_small/monet-avatar.jpg',
                    'medium' => 'https://api.freework.localhost/media/cache/resolve/user_avatar_medium/monet-avatar.jpg',
                ],
                'displayAvatar' => true,
                'forumPostUpvotesCount' => 2,
                'forumPostsCount' => 7,
                'createdAt' => '2020-01-01T10:00:00+01:00',
                'deleted' => false,
                'admin' => false,
            ],
            'contentHtml' => '<p>Category 1.1 - Topic 1 - Post 1 - Lorem</p>',
            'upvotesCount' => 0,
            'createdAt' => '2021-01-01T12:00:00+01:00',
            'updatedAt' => '2021-01-01T12:00:00+01:00',
            'deleted' => false,
            'moderated' => false,
        ]
        );
    }
}
