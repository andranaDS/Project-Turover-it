<?php

namespace App\JobPosting\Doctrine\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\JobPosting\Enum\Status;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CompanyJobPostingsExtension implements QueryCollectionExtensionInterface
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

        if ('api_job_postings_turnover_get_companies_slug_job_postings_collection' !== $request->attributes->get('_route')) {
            return;
        }

        $this->addWhere($queryBuilder, $resourceClass, $request);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass, Request $request): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->join(sprintf('%s.company', $rootAlias), 'c')
            ->where('c.slug = :slug')
            ->andWhere(sprintf('%s.status = :status', $rootAlias))
            ->setParameter('slug', $request->attributes->get('slug'))
            ->setParameter('status', Status::PUBLISHED)
        ;
    }
}
