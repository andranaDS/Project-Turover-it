<?php

namespace App\Core\Doctrine\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter as ApiPlatformSearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;

final class SearchFilter extends ApiPlatformSearchFilter
{
    // @phpstan-ignore-next-line
    protected function addWhereByStrategy(string $strategy, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $alias, string $field, $values, bool $caseSensitive): void
    {
        $newValues = [];

        if (!\is_array($values)) {
            $newValues = explode(',', $values);
        } else {
            foreach ($values as $value) {
                foreach (explode(',', $value) as $v) {
                    $newValues[] = $v;
                }
            }
        }

        parent::addWhereByStrategy($strategy, $queryBuilder, $queryNameGenerator, $alias, $field, $newValues, $caseSensitive);
    }
}
