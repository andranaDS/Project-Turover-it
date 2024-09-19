<?php

namespace App\User\Doctrine\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Recruiter\Entity\Recruiter;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Security;

class RecruiterCandidatesExtension implements QueryCollectionExtensionInterface
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
        if (null === $request = $this->requestStack->getMainRequest()) {
            return;
        }

        if ('api_users_turnover_get_recruiter_candidates_collection' !== $request->attributes->get('_route')) {
            return;
        }

        $this->addWhere($queryBuilder, $resourceClass, $request);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass, Request $request): void
    {
        if (!($recruiter = $this->security->getUser()) instanceof Recruiter) {
            throw new UnauthorizedHttpException('unauthorized');
        }

        /** @var Recruiter $recruiter */
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->andWhere(sprintf('%s.createdBy = :recruiterId', $rootAlias))
            ->setParameter('recruiterId', $recruiter->getId())
        ;
    }
}
