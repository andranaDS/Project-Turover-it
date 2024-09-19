<?php

namespace App\JobPosting\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;

class JobPostingMarketplaceDecorator implements OpenApiFactoryInterface
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
                'example' => 'intercontract,permanent,fixed-term,internship,contractor,apprenticeship',
                'schema' => [
                    'type' => 'string',
                ],
            ]),
            new \ArrayObject([
                'name' => 'minAnnualSalary',
                'in' => 'query',
                'description' => 'Minimum annual salary that the jobpostings should have',
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
                'description' => 'Minimum daily salary that the jobpostings should have',
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
            new \ArrayObject([
                'name' => 'startsAt',
                'in' => 'query',
                'description' => 'Filter on startsAt field. format: Y-m-d',
                'required' => false,
                'style' => 'simple',
                'example' => '2021-03-01',
                'schema' => [
                    'type' => 'string',
                ],
            ]),
            new \ArrayObject([
                'name' => 'businessActivity',
                'in' => 'query',
                'description' => 'Filter on business activity of company',
                'required' => false,
                'style' => 'simple',
                'example' => 'Filter by slug: agence-web-communication, cabinet-de-conseil, ...',
                'schema' => [
                    'type' => 'string',
                ],
            ]),
            new \ArrayObject([
                'name' => 'order',
                'in' => 'query',
                'description' => 'Order by relevance or date or salary(default value: date)',
                'required' => false,
                'style' => 'simple',
                'example' => 'salary',
                'schema' => [
                    'type' => 'string',
                ],
            ]),
        ];

        $content = new \ArrayObject([
            'application/ld+json' => [
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
                                'example' => 'intercontract',
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
                            'startsAt' => [
                                'type' => 'string',
                                'format' => 'date',
                                'example' => '2021-06-01',
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
                                        'example' => 'Lorem ipsum dolor',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]),
            ],
        ]);

        $openApi->getPaths()->addPath('/job_postings/marketplace', new PathItem(
            null,
            null,
            null,
            new Operation(
                'get_jobPostingsMarketplace',
                ['JobPosting'],
                [
                    Response::HTTP_OK => [
                        'description' => 'Get filtered list of jobPostings.',
                        'content' => $content,
                    ],
                    Response::HTTP_UNAUTHORIZED => [
                        'description' => 'The credentials are invalid',
                    ],
                    Response::HTTP_UNPROCESSABLE_ENTITY => [
                        'description' => 'Invalid input',
                    ],
                ],
                'Retrieves offers that have at least "intercontract" contract type.',
                'Retrieves offers that have at least "intercontract" contract type.',
                null,
                $filters,
                null,
            )
        ));

        return $openApi;
    }
}
