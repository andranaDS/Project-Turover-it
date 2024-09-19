<?php

namespace App\JobPosting\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;

final class JobPostingRecruiterFavoriteDecorator implements OpenApiFactoryInterface
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

        $openApi->getPaths()->addPath('/job_postings/{id}/recruiter/favorite', new PathItem(
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
                'patch_jobPostingRecruiterFavorite',
                ['JobPosting'],
                [
                    Response::HTTP_CREATED => [
                        'description' => 'JobPostingRecruiterFavorite resource created.',
                    ],
                    Response::HTTP_NO_CONTENT => [
                        'description' => 'JobPostingRecruiterFavorite resource deleted.',
                    ],
                    Response::HTTP_UNAUTHORIZED => [
                        'description' => 'The credentials are invalid',
                    ],
                ],
                'Add or delete an favorite on a JobPosting resource.',
                'Add or delete an favorite on a JobPosting resource.',
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
