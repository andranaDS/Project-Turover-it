<?php

namespace App\Recruiter\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;

final class RecruitersChangePasswordDecorator implements OpenApiFactoryInterface
{
    private OpenApiFactoryInterface $decorated;

    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        $openApi->getPaths()->addPath('/recruiters/{id}/change_password', new PathItem(
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
                'patch_RecruitersChangePassword',
                ['User'],
                [
                    Response::HTTP_UNPROCESSABLE_ENTITY => [
                        'description' => 'Unprocessable entity',
                    ],
                    Response::HTTP_OK => [
                        'description' => 'The password was changed',
                    ],
                ],
                'Change password',
                'Change password',
                null,
                [],
                new RequestBody(
                    '',
                    new \ArrayObject([
                        'application/json' => [
                            'schema' => new \ArrayObject([
                                'type' => 'object',
                                'properties' => [
                                    'oldPassword' => [
                                        'type' => 'string',
                                        'example' => 'OLD_PASSWORD',
                                    ],
                                    'newPassword' => [
                                        'type' => 'string',
                                        'example' => 'NEW_PASSWORD',
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
