<?php

namespace App\User\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;

final class ForgottenPasswordResetDecorator implements OpenApiFactoryInterface
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

        $openApi->getPaths()->addPath('/forgotten_password/reset', new PathItem(
            null,
            null,
            null,
            null,
            null,
            new Operation(
                'post_ForgottenPasswordReset',
                ['User'],
                [
                    Response::HTTP_BAD_REQUEST => [
                        'description' => 'Bad request, Token is missing, Token is not valid, Token is expired',
                    ],
                    Response::HTTP_OK => [
                        'description' => 'Token is valid and the user\'s password has been updated',
                    ],
                ],
                'Forgotten password reset',
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
                                    'plainPassword' => [
                                        'type' => 'string',
                                        'example' => 'N3wP@ssw0rd',
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
