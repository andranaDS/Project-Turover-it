<?php

namespace App\User\Doctrine\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CompanyCandidatesExtension implements QueryCollectionExtensionInterface
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        if (null === $request = $this->requestStack->getMainRequest()) {
            return;
        }

        if ('api_users_turnover_get_company_candidates_collection' !== $request->attributes->get('_route')) {
            return;
        }

        $this->addWhere($queryBuilder, $resourceClass, $request);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass, Request $request): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->join(sprintf('%s.createdBy', $rootAlias), 'r')
            ->join('r.company', 'c')
            ->where('c.slug = :slug')
            ->andWhere(sprintf('%s.visible = true', $rootAlias))
            ->setParameter('slug', $request->attributes->get('slug'))
        ;
    }
}
