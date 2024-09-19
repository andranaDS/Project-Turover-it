<?php

namespace App\JobPosting\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;

final class JobPostingDecorator implements OpenApiFactoryInterface
{
    private OpenApiFactoryInterface $decorated;

    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        $filters = [
            new \ArrayObject([
                'name' => 'searchKeywords',
                'in' => 'query',
                'description' => 'Search keywords in title, description and company description. Search tag can be separated by commas',
                'required' => false,
                'style' => 'simple',
                'example' => 'PHP Symfony, Javascript',
                'schema' => [
                    'type' => 'string',
                ],
            ]),
            new \ArrayObject([
                'name' => 'remoteMode',
                'in' => 'query',
                'description' => 'Filter on remote modes separated by commas',
                'required' => false,
                'style' => 'simple',
                'example' => 'partial,remote,full,none',
                'schema' => [
                    'type' => 'string',
                ],
            ]),
            new \ArrayObject([
                'name' => 'locationKeys',
                'in' => 'query',
                'description' => 'Location search key (COUNTRY_CODE~ADMIN_LEVEL_1_SLUG~ADMIN_LEVEL_2_SLUG)',
                'required' => false,
                'style' => 'simple',
                'example' => 'fr~ile-de-france~paris,fr~bourgogne',
                'schema' => [
                    'type' => 'string',
                ],
            ]),
            new \ArrayObject([
                'name' => 'contracts',
                'in' => 'query',
                'description' => 'Filter on contract type, separated by commas',
                'required' => false,
                'style' => 'simple',
                'example' => 'permanent,fixed-term,internship,contractor,apprenticeship',
                'schema' => [
                    'type' => 'string',
                ],
            ]),
            new \ArrayObject([
                'name' => 'minAnnualSalary',
                'in' => 'query',
                'description' => 'Minimum annual salary that the JobPostings should have',
                'required' => false,
                'style' => 'simple',
                'example' => '30000',
                'schema' => [
                    'type' => 'integer',
                ],
            ]),
            new \ArrayObject([
                'name' => 'minDailySalary',
                'in' => 'query',
                'description' => 'Minimum daily salary that the JobPostings should have',
                'required' => false,
                'style' => 'simple',
                'example' => '400',
                'schema' => [
                    'type' => 'integer',
                ],
            ]),
            new \ArrayObject([
                'name' => 'minDuration',
                'in' => 'query',
                'description' => 'Minimum job duration (in days)',
                'required' => false,
                'style' => 'simple',
                'example' => '30',
                'schema' => [
                    'type' => 'integer',
                ],
            ]),
            new \ArrayObject([
                'name' => 'maxDuration',
                'in' => 'query',
                'description' => 'Maximum job duration (in days)',
                'required' => false,
                'style' => 'simple',
                'example' => '90',
                'schema' => [
                    'type' => 'integer',
                ],
            ]),
            new \ArrayObject([
                'name' => 'publishedSince',
                'in' => 'query',
                'description' => 'Filter on published date. Choices: less_than_24_hours, from_1_to_7_days, from_8_to_14_days, from_15_days_to_1_month',
                'required' => false,
                'style' => 'simple',
                'example' => 'from_1_to_7_days',
                'schema' => [
                    'type' => 'string',
                ],
            ]),
            new \ArrayObject([
                'name' => 'itemsPerPage',
                'in' => 'query',
                'description' => 'Nb items max per page',
                'required' => false,
                'style' => 'simple',
                'example' => '5',
                'schema' => [
                    'type' => 'integer',
                ],
            ]),
            new \ArrayObject([
                'name' => 'skills',
                'in' => 'query',
                'description' => 'Filter on skills',
                'required' => false,
                'style' => 'simple',
                'example' => 'php,javascript',
                'schema' => [
                    'type' => 'string',
                ],
            ]),
        ];

        $content = new \ArrayObject([
            'application/json' => [
                'schema' => new \ArrayObject([
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => [
                                'type' => 'integer',
                                'example' => '22',
                            ],
                            'title' => [
                                'type' => 'string',
                                'format' => 'Développeur PHP Symfony',
                            ],
                            'description' => [
                                'type' => 'string',
                                'format' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                            ],
                            'experienceLevel' => [
                                'type' => 'string',
                                'format' => 'intermediate',
                            ],
                            'minAnnualSalary' => [
                                'type' => 'integer',
                                'example' => 30000,
                            ],
                            'minDailySalary' => [
                                'type' => 'integer',
                                'example' => 300,
                            ],
                            'currency' => [
                                'type' => 'string',
                                'example' => 'EUR',
                            ],
                            'contracts' => [
                                'type' => 'string',
                                'example' => 'permanent',
                            ],
                            'duration' => [
                                'type' => 'integer',
                                'example' => 36,
                            ],
                            'renewable' => [
                                'type' => 'boolean',
                                'example' => false,
                            ],
                            'remoteMode' => [
                                'type' => 'string',
                                'example' => 'partial',
                            ],
                            'relativeJobStart' => [
                                'type' => 'string',
                                'example' => 'next_month',
                            ],
                            'startsAt' => [
                                'type' => 'string',
                                'format' => 'date',
                                'example' => '2021-06-01',
                            ],
                            'salary' => [
                                'type' => 'string',
                                'example' => '€2,300.00',
                            ],
                            'publishedAt' => [
                                'type' => 'string',
                                'format' => 'date-time',
                                'example' => '2021-04-17T12:03:13+01:00',
                            ],
                            'published' => [
                                'type' => 'boolean',
                                'example' => true,
                            ],
                            'company' => [
                                'type' => 'object',
                                'properties' => [
                                    'id' => [
                                        'type' => 'string',
                                        'example' => '14',
                                    ],
                                    'name' => [
                                        'type' => 'string',
                                        'example' => 'FreeWork',
                                    ],
                                    'description' => [
                                        'type' => 'string',
                                        'example' => 'Blabla blablablabla blabla blabla blablablabla blabla',
                                    ],
                                    'logoUrl' => [
                                        'type' => 'string',
                                        'example' => 'https://www.freelance-info.fr/freelance-info/img/icons/AGSI_Logo.svg',
                                    ],
                                    'websiteUrl' => [
                                        'type' => 'https://www.freelance-info.fr',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]),
            ],
        ]);

        $openApi->getPaths()->addPath('/job_postings', new PathItem(
            null,
            null,
            null,
            new Operation(
                'get_jobPostings',
                ['JobPosting'],
                [
                    Response::HTTP_OK => [
                        'description' => 'Get filtered list of jobs.',
                        'content' => $content,
                    ],
                    Response::HTTP_UNAUTHORIZED => [
                        'description' => 'The credentials are invalid',
                    ],
                ],
                'Return a paginated and filtered list of jobs.',
                'Return a paginated list of jobs that can be filtered with optional filters.',
                null,
                $filters,
                null,
            )
        ));

        $openApi->getPaths()->addPath('/job_postings/count', new PathItem(
            null,
            null,
            null,
            new Operation(
                'get_jobPostingsCount',
                ['JobPosting'],
                [
                    Response::HTTP_OK => [
                        'description' => 'Get the total number of result matching filters.',
                        'content' => new \ArrayObject([
                            'application/json' => [
                                'schema' => new \ArrayObject([
                                    'type' => 'integer',
                                    'example' => '142',
                                ]),
                            ],
                        ]),
                    ],
                    Response::HTTP_UNAUTHORIZED => [
                        'description' => 'The credentials are invalid',
                    ],
                ],
                'Returns the number of job postings matching filters.',
                'Returns the total number of job postings matching filters.',
                null,
                $filters,
                null,
            )
        ));

        $openApi->getPaths()->addPath('/job_postings/suggested', new PathItem(
            null,
            null,
            null,
            new Operation(
                'get_jobPostingsSuggested',
                ['JobPosting'],
                [
                    Response::HTTP_OK => [
                        'description' => 'Retrieves the collection of suggested JobPosting resources of a User resource.',
                        'content' => $content,
                    ],
                    Response::HTTP_UNAUTHORIZED => [
                        'description' => 'The credentials are invalid',
                    ],
                ],
                'Retrieves the collection of suggested JobPosting resources of a User resource.',
                'Retrieves the collection of suggested JobPosting resources of a User resource.',
                null,
                $filters,
                null,
            )
        ));

        return $openApi;
    }
}
