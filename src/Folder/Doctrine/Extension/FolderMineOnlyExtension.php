<?php

namespace App\Folder\Doctrine\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Folder\Entity\Folder;
use App\Recruiter\Entity\Recruiter;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;

class FolderMineOnlyExtension implements QueryCollectionExtensionInterface
{
    protected Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        if (Folder::class !== $resourceClass || 'turnover_get' !== $operationName) {
            return;
        }

        $recruiter = $this->security->getUser();
        if (!$recruiter instanceof Recruiter) {
            throw new AuthenticationException();
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->andWhere(sprintf('%s.recruiter = :recruiter', $rootAlias))
            ->setParameter('recruiter', $recruiter)
        ;
    }
}
