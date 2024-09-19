<?php

namespace App\Blog\Tests\Functional\BlogPost;

use App\Tests\Functional\ApiTestCase;

class BlogPostMostViewedTest extends ApiTestCase
{
    public static function provideDataCases(): iterable
    {
        yield [
            'en-gb',
            [
                '@context' => '/contexts/BlogPost',
                '@id' => '/blog_posts',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    [
                        '@id' => '/blog_posts/blog-category-2-post-1',
                        '@type' => 'BlogPost',
                        'title' => 'Blog - Category 2 - Post 1',
                        'slug' => 'blog-category-2-post-1',
                        'publishedAt' => '2021-01-01T12:00:00+01:00',
                        'updatedAt' => '2021-01-01T12:00:00+01:00',
                        'modified' => true,
                    ],
                ],
            ],
        ];

        yield [
            'fr',
            [
                '@context' => '/contexts/BlogPost',
                '@id' => '/blog_posts',
                '@type' => 'hydra:Collection',
                'hydra:member' => [
                    [
                        '@id' => '/blog_posts/blog-category-2-post-1',
                        '@type' => 'BlogPost',
                        'title' => 'Blog - Category 2 - Post 1',
                        'slug' => 'blog-category-2-post-1',
                        'publishedAt' => '2021-01-01T12:00:00+01:00',
                        'updatedAt' => '2021-01-01T12:00:00+01:00',
                        'modified' => true,
                    ],
                    [
                        '@id' => '/blog_posts/blog-category-1-post-2',
                        '@type' => 'BlogPost',
                        'title' => 'Blog - Category 1 - Post 2',
                        'slug' => 'blog-category-1-post-2',
                        'publishedAt' => '2021-01-01T11:00:00+01:00',
                        'updatedAt' => '2021-01-01T11:00:00+01:00',
                        'modified' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider  provideDataCases
     */
    public function testData(string $locale, array $expected): void
    {
        $client = static::createFreeWorkClient();
        $a = $client->request('GET', '/blog_posts/most_viewed', [
            'headers' => [
                'accept-language' => $locale,
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains($expected);
    }
}
