<?php

namespace App\Forum\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;

final class ForumStatisticsDecorator implements OpenApiFactoryInterface
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

        $openApi->getPaths()->addPath('/forum/statistics', new PathItem(
            null,
            null,
            null,
            new Operation(
                'get_forumStatistics',
                ['Forum'],
                [
                    Response::HTTP_OK => [
                        'description' => '',
                        'content' => new \ArrayObject([
                            'application/json' => [
                                'schema' => new \ArrayObject([
                                    'type' => 'object',
                                    'properties' => [
                                        'topicsCount' => [
                                            'type' => 'integer',
                                            'example' => 1,
                                        ],
                                        'postsCount' => [
                                            'type' => 'integer',
                                            'example' => 2,
                                        ],
                                        'recentPostsCount' => [
                                            'type' => 'integer',
                                            'example' => 3,
                                        ],
                                        'contributorsCount' => [
                                            'type' => 'integer',
                                            'example' => 4,
                                        ],
                                        'forumActiveUsersCount' => [
                                            'type' => 'integer',
                                            'example' => 5,
                                        ],
                                    ],
                                ]),
                            ],
                        ]),
                    ],
                ],
                'Retrieves the forum statistics.',
                'Retrieves the forum statistics.',
            )
        ));

        return $openApi;
    }
}
