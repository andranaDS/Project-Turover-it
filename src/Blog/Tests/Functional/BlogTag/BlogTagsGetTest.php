<?php

namespace App\Blog\Tests\Functional\BlogTag;

use App\Tests\Functional\ApiTestCase;

class BlogTagsGetTest extends ApiTestCase
{
    public function testWithoutFilter(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_tags');
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/BlogTag',
            '@id' => '/blog_tags',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/blog_tags/blog-tag-1',
                    '@type' => 'BlogTag',
                    'id' => 1,
                    'name' => 'Blog - Tag 1',
                    'slug' => 'blog-tag-1',
                ],
                [
                    '@id' => '/blog_tags/blog-tag-2',
                    '@type' => 'BlogTag',
                    'id' => 2,
                    'name' => 'Blog - Tag 2',
                    'slug' => 'blog-tag-2',
                ],
            ],
            'hydra:totalItems' => 4,
            'hydra:view' => [
                '@id' => '/blog_tags?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/blog_tags?page=1',
                'hydra:last' => '/blog_tags?page=2',
                'hydra:next' => '/blog_tags?page=2',
            ],
            'hydra:search' => [
                '@type' => 'hydra:IriTemplate',
                'hydra:template' => '/blog_tags{?properties[]}',
                'hydra:variableRepresentation' => 'BasicRepresentation',
                'hydra:mapping' => [
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'properties[]',
                        'property' => null,
                        'required' => false,
                    ],
                ],
            ],
        ]);
    }

    public function testWithPropertiesFilter(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_tags?pagination=false&properties[]=slug');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonEquals([
            '@context' => '/contexts/BlogTag',
            '@id' => '/blog_tags',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/blog_tags/blog-tag-1',
                    '@type' => 'BlogTag',
                    'slug' => 'blog-tag-1',
                ],
                [
                    '@id' => '/blog_tags/blog-tag-2',
                    '@type' => 'BlogTag',
                    'slug' => 'blog-tag-2',
                ],
                [
                    '@id' => '/blog_tags/blog-tag-3',
                    '@type' => 'BlogTag',
                    'slug' => 'blog-tag-3',
                ],
                [
                    '@id' => '/blog_tags/blog-tag-4',
                    '@type' => 'BlogTag',
                    'slug' => 'blog-tag-4',
                ],
            ],
            'hydra:totalItems' => 4,
            'hydra:view' => [
                '@id' => '/blog_tags?pagination=false&properties%5B%5D=slug',
                '@type' => 'hydra:PartialCollectionView',
            ],
            'hydra:search' => [
                '@type' => 'hydra:IriTemplate',
                'hydra:template' => '/blog_tags{?properties[]}',
                'hydra:variableRepresentation' => 'BasicRepresentation',
                'hydra:mapping' => [
                    [
                        '@type' => 'IriTemplateMapping',
                        'variable' => 'properties[]',
                        'property' => null,
                        'required' => false,
                    ],
                ],
            ],
        ]);
    }
}
