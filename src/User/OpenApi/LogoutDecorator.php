<?php

namespace App\User\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;

final class LogoutDecorator implements OpenApiFactoryInterface
{
    private OpenApiFactoryInterface $decorated;

    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        $openApi->getPaths()->addPath('/logout', new PathItem(
            null,
            null,
            null,
            new Operation(
                'get_AuthLogout',
                ['Auth'],
                [
                    Response::HTTP_OK => [
                        'description' => 'JWT and refresh token cookies are deleted',
                    ],
                ],
                'Delete JWT and refresh token cookies to logout.',
            )
        ));

        return $openApi;
    }
}
