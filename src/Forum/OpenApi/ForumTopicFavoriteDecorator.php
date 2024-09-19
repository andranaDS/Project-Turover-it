<?php

namespace App\Forum\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;

final class ForumTopicFavoriteDecorator implements OpenApiFactoryInterface
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

        $openApi->getPaths()->addPath('/forum_topics/{slug}/favorite', new PathItem(
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            new Operation(
                'patch_forumTopicFavorite',
                ['ForumTopic'],
                [
                    Response::HTTP_CREATED => [
                        'description' => 'ForumTopicFavorite resource created.',
                    ],
                    Response::HTTP_NOT_IMPLEMENTED => [
                        'description' => 'ForumTopicFavorite resource deleted.',
                    ],
                    Response::HTTP_UNAUTHORIZED => [
                        'description' => 'ForumTopicFavorite resource deleted.',
                    ],
                ],
                'Add or delete an favorite on a ForumTopic resource.',
                'Add or delete an favorite on a ForumTopic resource.',
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
                ],
            )
        ));

        return $openApi;
    }
}
