<?php

namespace App\Company\Doctrine\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CompaniesRecruiterFavoritesExtension implements QueryCollectionExtensionInterface
{
    private RequestStack $requestStack;
    private Security $security;

    public function __construct(RequestStack $requestStack, Security $security)
    {
        $this->requestStack = $requestStack;
        $this->security = $security;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        if ((null === $request = $this->requestStack->getMainRequest()) || (null === $recruiter = $this->security->getUser())) {
            return;
        }

        if ('api_companies_turnover_get_recruiter_favorites_collection' !== $request->attributes->get('_route')) {
            return;
        }

        $this->addWhere($queryBuilder, $recruiter);
    }

    private function addWhere(QueryBuilder $queryBuilder, UserInterface $recruiter): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->join(sprintf('%s.recruiterFavorites', $rootAlias), 'rf')
            ->andWhere('rf.recruiter = :recruiter')
            ->setParameter('recruiter', $recruiter)
            ->addOrderBy('rf.createdAt', Criteria::DESC)
        ;
    }
}
