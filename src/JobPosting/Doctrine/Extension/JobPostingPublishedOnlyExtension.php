<?php

namespace App\JobPosting\Doctrine\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\JobPosting\Entity\JobPosting;
use Doctrine\ORM\QueryBuilder;

class JobPostingPublishedOnlyExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        if (JobPosting::class !== $resourceClass || !\in_array($operationName, [
            'freework_get_companies_slug_job_postings',
            'freework_get_favorites',
        ], true)) {
            return;
        }

        $this->addWhere($queryBuilder);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = []): void
    {
        if (JobPosting::class !== $resourceClass || 'freework_get' !== $operationName) {
            return;
        }

        $this->addWhere($queryBuilder);
    }

    private function addWhere(QueryBuilder $queryBuilder): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->andWhere(sprintf('%s.published = true AND %s.publishedAt <= :now', $rootAlias, $rootAlias))
            ->setParameter('now', new \DateTime('now'))
        ;
    }
}
