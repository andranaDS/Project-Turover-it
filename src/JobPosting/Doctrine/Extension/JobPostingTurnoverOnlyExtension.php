<?php

namespace App\JobPosting\Doctrine\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Enum\Contract;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class JobPostingTurnoverOnlyExtension implements QueryItemExtensionInterface, QueryCollectionExtensionInterface
{
    protected ContainerInterface $container;
    protected RequestStack $requestStack;

    public function __construct(ContainerInterface $container, RequestStack $requestStack)
    {
        $this->container = $container;
        $this->requestStack = $requestStack;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        if (JobPosting::class !== $resourceClass) {
            return;
        }

        if ('turnover_get_companies_slug_job_postings' !== $operationName) {
            return;
        }

        $this->andWhere($queryBuilder);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = []): void
    {
    }

    public function andWhere(QueryBuilder $queryBuilder): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];

        $orX = $queryBuilder->expr()->orX();

        foreach (Contract::getTurnoverValues() as $key => $contract) {
            $arg = 'contract_' . $key;
            $orX->add("JSON_CONTAINS($rootAlias.contracts, :$arg) = 1");
            $queryBuilder->setParameter($arg, sprintf('"%s"', $contract));
        }

        $queryBuilder->andWhere($orX);
    }
}
