<?php

namespace App\User\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;

final class ChangeEmailRequestDecorator implements OpenApiFactoryInterface
{
    private OpenApiFactoryInterface $decorated;

    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        $openApi->getPaths()->addPath('/change_email/request', new PathItem(
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
                'patch_ChangeEmailRequest',
                ['User'],
                [
                    Response::HTTP_UNPROCESSABLE_ENTITY => [
                        'description' => 'Unprocessable entity',
                    ],
                    Response::HTTP_OK => [
                        'description' => 'The change email process has been initialized and an email was sent',
                    ],
                ],
                'Change email request',
                'Change email request',
                null,
                [],
                new RequestBody(
                    '',
                    new \ArrayObject([
                        'application/json' => [
                            'schema' => new \ArrayObject([
                                'type' => 'object',
                                'properties' => [
                                    'email' => [
                                        'type' => 'string',
                                        'example' => 'new-email@free-work.fr',
                                    ],
                                ],
                            ]),
                        ],
                    ]),
                ),
            )
        ));

        return $openApi;
    }
}
