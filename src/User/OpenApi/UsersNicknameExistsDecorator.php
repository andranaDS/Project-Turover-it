<?php

namespace App\User\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;

final class UsersNicknameExistsDecorator implements OpenApiFactoryInterface
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

        $openApi->getPaths()->addPath('/users/exists/{nickname}', new PathItem(
            null,
            null,
            null,
            new Operation(
                'get_nickname_exists',
                ['User'],
                [
                    Response::HTTP_OK => [
                        'description' => 'Nickname exists, not available nickname',
                    ],
                    Response::HTTP_NOT_FOUND => [
                        'description' => 'Nickname not exists, available nickname',
                    ],
                ],
                'Check that the nickname exists.',
                'Check that the nickname exists',
                null,
                [
                    new \ArrayObject([
                        'name' => 'nickname',
                        'in' => 'path',
                        'description' => 'Resource identifier',
                        'required' => true,
                        'style' => 'simple',
                        'schema' => [
                            'type' => 'string',
                        ],
                    ]),
                ],
            ),
            null,
            null,
            null,
            null,
            null,
        ));

        return $openApi;
    }
}
