<?php

namespace App\JobPosting\Doctrine\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\JobPosting\Entity\JobPosting;
use App\Recruiter\Entity\Recruiter;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

class JobPostingRecruitersMeExtension implements QueryCollectionExtensionInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        if (JobPosting::class === $resourceClass && 'turnover_get_recruiters_me_job_postings' === $operationName) {
            $this->andWhere($queryBuilder);
        }
    }

    private function andWhere(QueryBuilder $queryBuilder): void
    {
        if (!($recruiter = $this->security->getUser()) instanceof Recruiter) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder->andWhere("$rootAlias.assignedTo = :assignedTo")
            ->setParameter('assignedTo', $recruiter)
        ;
    }
}
