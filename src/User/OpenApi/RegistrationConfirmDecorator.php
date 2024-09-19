<?php

namespace App\User\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;

final class RegistrationConfirmDecorator implements OpenApiFactoryInterface
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

        $openApi->getPaths()->addPath('/registration/confirm', new PathItem(
            null,
            null,
            null,
            null,
            null,
            new Operation(
                'post_RegistrationConfirm',
                ['User'],
                [
                    Response::HTTP_BAD_REQUEST => [
                        'description' => 'Bad request, Token is missing, Token is not valid',
                    ],
                    Response::HTTP_OK => [
                        'description' => 'Token is valid, the user has been enabled',
                    ],
                ],
                'Registration email confirmaton',
                'Forgotten password reset',
                null,
                [],
                new RequestBody(
                    '',
                    new \ArrayObject([
                        'application/json' => [
                            'schema' => new \ArrayObject([
                                'type' => 'object',
                                'properties' => [
                                    'token' => [
                                        'type' => 'string',
                                        'example' => 'S3cr3tT0k3n',
                                    ],
                                ],
                            ]),
                        ],
                    ]),
                ),
            ),
        ));

        return $openApi;
    }
}
