<?php

declare(strict_types=1);

namespace App\Core\Doctrine\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Carbon\Carbon;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

final class RangeEnumFilter extends AbstractFilter
{
    public const TODAY = 'today';
    public const YESTERDAY = 'yesterday';
    public const LAST_WEEK = 'last_week';
    public const LAST_MONTH = 'last_month';

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        if (
            !isset($value['enum_range']) ||
            !$this->isPropertyEnabled($property, $resourceClass) ||
            !$this->isPropertyMapped($property, $resourceClass)
        ) {
            return;
        }

        $range = $this->getRange($value['enum_range']);
        if (null === $range) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];

        $start = $range['start'] ?? null;
        if (null !== $start) {
            $startParameterName = $queryNameGenerator->generateParameterName($property);
            $queryBuilder
                ->andWhere(":$startParameterName <= $alias.$property")
                ->setParameter($startParameterName, $start)
            ;
        }

        $end = $range['end'] ?? null;
        if (null !== $end) {
            $endParameterName = $queryNameGenerator->generateParameterName($property);
            $queryBuilder
                ->andWhere("$alias.$property <= :$endParameterName")
                ->setParameter($endParameterName, $end)
            ;
        }
    }

    private function getRange(string $value): ?array
    {
        return match ($value) {
            self::TODAY => [
                'start' => Carbon::today()->format('Y-m-d H:i:s'),
            ],
            self::YESTERDAY => [
                'start' => Carbon::yesterday()->format('Y-m-d H:i:s'),
                'end' => Carbon::yesterday()->endOfDay()->format('Y-m-d H:i:s'),
            ],
            self::LAST_WEEK => [
                'start' => Carbon::today()->subDays(7)->format('Y-m-d H:i:s'),
            ],
            self::LAST_MONTH => [
                'start' => Carbon::today()->subDays(30)->format('Y-m-d H:i:s'),
            ],
            default => null,
        };
    }

    public function getDescription(string $resourceClass): array
    {
        if (!$this->properties) {
            return [];
        }

        $description = [];
        foreach ($this->properties as $property => $strategy) {
            $description[$property . '[range_enum]'] = [
                'property' => $property,
                'type' => Type::BUILTIN_TYPE_STRING,
                'required' => false,
                'openapi' => [
                    'example' => '"today", "yesterday", "last_week" or "last_month"',
                    'allowReserved' => false,
                    'allowEmptyValue' => true,
                    'explode' => false,
                ],
            ];
        }

        return $description;
    }
}
