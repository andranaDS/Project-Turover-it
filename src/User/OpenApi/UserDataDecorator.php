<?php

namespace App\User\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;

final class UserDataDecorator implements OpenApiFactoryInterface
{
    private OpenApiFactoryInterface $decorated;

    public function __construct(
        OpenApiFactoryInterface $decorated
    ) {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        $openApi->getPaths()->addPath('/user/{id}/data', new PathItem(
            null,
            null,
            null,
            new Operation(
                'get_userData',
                ['User'],
                [
                    Response::HTTP_OK => [
                        'description' => 'Retrieves the user data',
                        'content' => new \ArrayObject([
                            'application/json' => [
                                'schema' => new \ArrayObject([
                                    'type' => 'object',
                                    'properties' => [
                                        'forum_topic_traces' => [
                                            'type' => 'object',
                                            'properties' => [
                                                '1' => [
                                                    'type' => 'string',
                                                    'format' => 'date-time',
                                                    'example' => '2021-01-01 13:37:00',
                                                ],
                                                '2' => [
                                                    'type' => 'string',
                                                    'format' => 'date-time',
                                                    'example' => '2021-01-01 13:37:00',
                                                ],
                                            ],
                                        ],
                                        'forum_topic_favorites' => [
                                            'type' => 'array',
                                            'items' => [
                                                'type' => 'integer',
                                                'example' => [3, 4, 5],
                                            ],
                                        ],
                                        'forum_post_upvotes' => [
                                            'type' => 'array',
                                            'items' => [
                                                'type' => 'integer',
                                                'example' => [1, 2, 5],
                                            ],
                                        ],
                                        'blog_post_upvotes' => [
                                            'type' => 'array',
                                            'items' => [
                                                'type' => 'integer',
                                                'example' => [3, 4, 5],
                                            ],
                                        ],
                                        'company_favorites' => [
                                            'type' => 'array',
                                            'items' => [
                                                'type' => 'integer',
                                                'example' => [1, 2],
                                            ],
                                        ],
                                        'company_blacklists' => [
                                            'type' => 'array',
                                            'items' => [
                                                'type' => 'integer',
                                                'example' => [3, 4, 5],
                                            ],
                                        ],
                                        'job_posting_favorites' => [
                                            'type' => 'array',
                                            'items' => [
                                                'type' => 'integer',
                                                'example' => [1, 2],
                                            ],
                                        ],
                                    ],
                                ]),
                            ],
                        ]),
                    ],
                    Response::HTTP_UNAUTHORIZED => [
                        'description' => 'The credentials are invalid',
                    ],
                ],
                'Retrieves the user data (forum_topic_traces, forum_topic_favorites, forum_post_upvotes, blog_post_upvotes, company_favorites, company_blacklists, job_posting_favorites) of the user.',
                'Retrieves the user data (forum_topic_traces, forum_topic_favorites, forum_post_upvotes, blog_post_upvotes, company_favorites, company_blacklists, job_posting_favorites) of the user.',
                null,
                [
                    new \ArrayObject([
                        'name' => 'scopes',
                        'in' => 'query',
                        'description' => 'The scope must be a sub selections of the following elements `forum_topic_traces`, `forum_topic_favorites`, `forum_post_upvotes`, `blog_post_upvotes`, `company_favorites`, `company_blacklists`, `job_posting_favorites` separated by a comma. Example : `forum_topic_traces,forum_topic_favorites`',
                        'required' => false,
                        'style' => 'simple',
                        'schema' => [
                            'type' => 'string',
                        ],
                    ]),
                ],
                null,
            )
        ));

        return $openApi;
    }
}
