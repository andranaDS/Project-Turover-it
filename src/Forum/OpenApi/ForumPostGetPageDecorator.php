<?php

namespace App\Forum\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;

final class ForumPostGetPageDecorator implements OpenApiFactoryInterface
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

        $openApi->getPaths()->addPath('/forum_posts/{id}/page', new PathItem(
            null,
            null,
            null,
            new Operation(
                'get_page',
                ['ForumPost'],
                [
                    Response::HTTP_OK => [
                        'description' => '',
                        'content' => new \ArrayObject([
                            'application/json' => [
                                'schema' => new \ArrayObject([
                                    'type' => 'object',
                                    'properties' => [
                                        'page' => [
                                            'type' => 'integer',
                                            'example' => 2,
                                        ],
                                    ],
                                ]),
                            ],
                        ]),
                    ],
                ],
                'Retrieves page number of the current forum post.',
                'Retrieves page number of the current forum post',
                null,
                [
                    new \ArrayObject([
                        'name' => 'id',
                        'in' => 'path',
                        'description' => 'Resource identifier',
                        'required' => true,
                        'style' => 'simple',
                        'schema' => [
                            'type' => 'string',
                        ],
                    ]),
                    new \ArrayObject([
                        'name' => 'itemsPerPage',
                        'in' => 'query',
                        'description' => 'Number of items per page',
                        'required' => true,
                        'style' => 'simple',
                        'example' => '5',
                        'schema' => [
                            'type' => 'integer',
                        ],
                    ]),
                ],
            )
        ));

        return $openApi;
    }
}
