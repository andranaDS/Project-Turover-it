<?php

namespace App\Blog\Tests\Functional\BlogCategory;

use App\Tests\Functional\ApiTestCase;

class BlogCategoriesGetTest extends ApiTestCase
{
    public function testWithoutLocale(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_categories');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        self::assertJsonContains([
            '@context' => '/contexts/BlogCategory',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@type' => 'BlogCategory',
                    'name' => 'Blog - Category 1',
                    'slug' => 'blog-category-1',
                    'description' => 'Blog - Category 1 // Description',
                    'postsCount' => 4,
                ],
                [
                    '@type' => 'BlogCategory',
                    'name' => 'Blog - Category 3',
                    'slug' => 'blog-category-3',
                    'description' => 'Blog - Category 3 // Description',
                    'postsCount' => 3,
                ],
            ],
            'hydra:totalItems' => 2,
        ]);
    }

    public function testWithLocaleEqualsToDefaultLocale(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_categories', [
            'headers' => [
                'Accept-Language' => 'fr',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/BlogCategory',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@type' => 'BlogCategory',
                    'name' => 'Blog - Category 1',
                    'slug' => 'blog-category-1',
                    'description' => 'Blog - Category 1 // Description',
                    'postsCount' => 4,
                ],
                [
                    '@type' => 'BlogCategory',
                    'name' => 'Blog - Category 3',
                    'slug' => 'blog-category-3',
                    'description' => 'Blog - Category 3 // Description',
                    'postsCount' => 3,
                ],
            ],
            'hydra:totalItems' => 2,
        ]);
    }

    public function testWithOtherLocale(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_categories', [
            'headers' => [
                'Accept-Language' => 'fr-be',
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        self::assertJsonContains([
            '@context' => '/contexts/BlogCategory',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@type' => 'BlogCategory',
                    'name' => 'Blog - Category 1',
                    'slug' => 'blog-category-1',
                    'description' => 'Blog - Category 1 // Description',
                    'postsCount' => 4,
                ],
                [
                    '@type' => 'BlogCategory',
                    'name' => 'Blog - Category 2',
                    'slug' => 'blog-category-2',
                    'description' => 'Blog - Category 2 // Description',
                    'postsCount' => 1,
                ],
            ],
            'hydra:totalItems' => 3,
        ]);
    }

    public function testWithPropertiesFilter(): void
    {
        $client = static::createFreeWorkClient();
        $client->request('GET', '/blog_categories?pagination=false&properties[]=slug');

        self::assertJsonEquals([
            '@context' => '/contexts/BlogCategory',
            '@id' => '/blog_categories',
            '@type' => 'hydra:Collection',
            'hydra:member' => [
                [
                    '@id' => '/blog_categories/blog-category-1',
                    '@type' => 'BlogCategory',
                    'slug' => 'blog-category-1',
                ],
                [
                    '@id' => '/blog_categories/blog-category-3',
                    '@type' => 'BlogCategory',
                    'slug' => 'blog-category-3',
                ],
            ],
            'hydra:totalItems' => 2,
            'hydra:view' => [
                '@id' => '/blog_categories?pagination=false&properties%5B%5D=slug',
                '@type' => 'hydra:PartialCollectionView',
            ],
            'hydra:search' => [
                '@type' => 'hydra:IriTemplate',
                'hydra:template' => '/blog_categories{?properties[]}',
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
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }
}
