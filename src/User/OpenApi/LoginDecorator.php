<?php

namespace App\User\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;

final class LoginDecorator implements OpenApiFactoryInterface
{
    private OpenApiFactoryInterface $decorated;
    private string $authDocumentationEmail;
    private string $authDocumentationPassword;

    public function __construct(
        OpenApiFactoryInterface $decorated,
        ParameterBagInterface $params
    ) {
        $this->decorated = $decorated;
        $this->authDocumentationEmail = $params->get('authDocumentationEmail');
        $this->authDocumentationPassword = $params->get('authDocumentationPassword');
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        $openApi->getPaths()->addPath('/login', new PathItem(
            null,
            null,
            null,
            null,
            null,
            new Operation(
                'get_AuthLogin',
                ['Auth'],
                [
                    Response::HTTP_NO_CONTENT => [
                        'description' => 'JWT and refresh token are stored in cookies httpOnly',
                    ],
                    Response::HTTP_UNAUTHORIZED => [
                        'description' => 'The credentials are invalid',
                    ],
                ],
                'Create new JWT and refresh token cookies to login.',
                '',
                null,
                [],
                new RequestBody(
                    'Generate new JWT Token',
                    new \ArrayObject([
                        'application/json' => [
                            'schema' => new \ArrayObject([
                                'type' => 'object',
                                'properties' => [
                                    'email' => [
                                        'type' => 'string',
                                        'example' => $this->authDocumentationEmail,
                                    ],
                                    'password' => [
                                        'type' => 'string',
                                        'example' => $this->authDocumentationPassword,
                                    ],
                                    'remember' => [
                                        'type' => 'boolean',
                                        'example' => true,
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
