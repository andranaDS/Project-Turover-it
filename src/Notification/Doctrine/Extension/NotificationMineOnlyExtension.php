<?php

namespace App\Notification\Doctrine\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Notification\Entity\Notification;
use App\Recruiter\Entity\Recruiter;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

class NotificationMineOnlyExtension implements QueryCollectionExtensionInterface
{
    protected Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        if (Notification::class !== $resourceClass
            || 'turnover_get' !== $operationName
            || !($recruiter = $this->security->getUser()) instanceof Recruiter
        ) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->where("$rootAlias.recruiter = :recruiter")
            ->setParameters([
                'recruiter' => $recruiter,
            ])
        ;
    }
}
