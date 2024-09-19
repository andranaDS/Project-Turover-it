<?php

namespace App\Core\Doctrine\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Core\Entity\Location;
use Doctrine\ORM\QueryBuilder;

final class LocationFilter extends AbstractContextAwareFilter
{
    // @phpstan-ignore-next-line
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        $filterName = $property;
        $filterProperty = 'location';

        if (
            !$this->isPropertyEnabled($filterName, $resourceClass) ||
            !$this->isPropertyMapped($filterProperty, $resourceClass)
        ) {
            return;
        }

        if (!\is_array($value)) {
            $value = explode(',', $value);
        }

        $locationKeys = [];
        foreach ($value as $v) {
            $locationKeys[] = Location::explodeKey($v);
        }

        $locationKeys = array_filter($locationKeys);

        if (empty($locationKeys)) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];

        $orExp = $queryBuilder->expr()->orX();

        foreach ($locationKeys as $locationKey) {
            $andExp = $queryBuilder->expr()->andX();

            foreach ($locationKey as $p => $v) {
                $andExp->add($queryBuilder->expr()->eq(sprintf('%s.%s.%s', $alias, $filterProperty, $p), $queryBuilder->expr()->literal($v)));
            }

            $orExp->add($andExp);
        }

        $queryBuilder->andWhere($orExp);
    }

    public function getDescription(string $resourceClass): array
    {
        if (!$this->properties) {
            return [];
        }

        $description = [];
        foreach ($this->properties as $property => $strategy) {
            $description["$property"] = [
                'property' => $property,
                'type' => 'string',
                'required' => false,
                'description' => 'Example: fr~ile-de-france~~vincennes / fr~ile-de-france~val-de-marne~/ fr~ile-de-france~~ / fr~~~',
            ];
            $description["$property" . '[]'] = [
                'property' => "$property" . '[]',
                'type' => 'array',
                'required' => false,
                'schema' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                    ],
                ],
                'description' => 'Example: fr~ile-de-france~~vincennes / fr~ile-de-france~val-de-marne~/ fr~ile-de-france~~ / fr~~~',
            ];
        }

        return $description;
    }
}
