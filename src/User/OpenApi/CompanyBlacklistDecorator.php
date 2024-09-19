<?php

namespace App\User\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;

final class CompanyBlacklistDecorator implements OpenApiFactoryInterface
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

        $openApi->getPaths()->addPath('/companies/{id}/blacklist', new PathItem(
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
                'patch_companyBlacklist',
                ['Company'],
                [
                    Response::HTTP_CREATED => [
                        'description' => 'CompanyBlacklist resource created.',
                    ],
                    Response::HTTP_NO_CONTENT => [
                        'description' => 'CompanyBlacklist resource deleted.',
                    ],
                    Response::HTTP_UNAUTHORIZED => [
                        'description' => 'The credentials are invalid',
                    ],
                ],
                'Add or delete a Company resource to the blacklist.',
                'Add or delete a Company resource to the blacklist.',
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
