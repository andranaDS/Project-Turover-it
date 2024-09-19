<?php

namespace App\Core\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;

final class GeoAutocompleteDecorator implements OpenApiFactoryInterface
{
    private OpenApiFactoryInterface $decorated;

    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        $openApi->getPaths()->addPath('/geo_autocomplete', new PathItem(
            null,
            null,
            null,
            new Operation(
                'get_geoAutocomplete',
                ['Core'],
                [
                    Response::HTTP_OK => [
                        'description' => 'Returns locations for autocompletion',
                        'content' => new \ArrayObject([
                            'application/json' => [
                                'schema' => new \ArrayObject([
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'key' => [
                                                'type' => 'string',
                                                'example' => 'fr~ile-de-france~~vincennes',
                                            ],
                                            'label' => [
                                                'type' => 'string',
                                                'example' => 'Vincennes, Ile-de-France',
                                            ],
                                            'shortLabel' => [
                                                'type' => 'string',
                                                'example' => 'Vincennes (94)',
                                            ],
                                            'latitude' => [
                                                'type' => 'string',
                                                'format' => '48.8534',
                                            ],
                                            'longitude' => [
                                                'type' => 'string',
                                                'format' => '2.3488',
                                            ],
                                            'adminLevel1' => [
                                                'type' => 'string',
                                                'format' => 'Ile-de-France',
                                            ],
                                            'adminLevel2' => [
                                                'type' => 'string',
                                                'format' => 'Vincennes',
                                            ],
                                            'country' => [
                                                'type' => 'string',
                                                'example' => 'France',
                                            ],
                                            'countryCode' => [
                                                'type' => 'string',
                                                'example' => 'FR',
                                            ],
                                        ],
                                    ],
                                ]),
                            ],
                        ]),
                    ],
                    Response::HTTP_UNAUTHORIZED => [
                        'description' => 'The credentials are invalid',
                    ],
                ],
                'Return a limited list of geo location matching the search parameter.',
                'Return a limited list of geo location matching the search parameter. Works as autocompletion',
                null,
                [
                    new \ArrayObject([
                        'name' => 'search',
                        'in' => 'query',
                        'description' => 'The location search for autocompletion',
                        'required' => true,
                        'style' => 'simple',
                        'example' => 'Pari',
                        'schema' => [
                            'type' => 'string',
                        ],
                    ]),
                    new \ArrayObject([
                        'name' => 'closestToPoint',
                        'in' => 'query',
                        'description' => 'Return the closest results from this location coords',
                        'required' => false,
                        'style' => 'simple',
                        'example' => '48.8534,2.3488',
                        'schema' => [
                            'type' => 'string',
                        ],
                    ]),
                ],
                null,
            )
        ));

        return $openApi;
    }
}
