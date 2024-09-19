<?php

namespace App\JobPosting\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;

class JobPostingUnpublishDecorator implements OpenApiFactoryInterface
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

        $openApi->getPaths()->addPath('/job_postings/{id}/unpublish', new PathItem(
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
                'patch_jobPostingUnpublish',
                ['JobPosting'],
                [
                    Response::HTTP_OK => [
                        'description' => 'JobPostingUnpublish resource unpublish.',
                    ],
                    Response::HTTP_NOT_FOUND => [
                        'description' => 'JobPostingUnpublish resource not found .',
                    ],
                    Response::HTTP_FORBIDDEN => [
                        'description' => 'JobPostingUnpublish forbidden contents .',
                    ],
                    Response::HTTP_UNAUTHORIZED => [
                        'description' => 'The credentials are invalid',
                    ],
                ],
                'Unpublish a JobPosting resource.',
                'Unpublish a JobPosting resource.',
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
