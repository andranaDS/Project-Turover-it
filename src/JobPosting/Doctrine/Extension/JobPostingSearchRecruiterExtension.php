<?php

namespace App\JobPosting\Doctrine\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\JobPosting\Traits\JobPostingRecruiterSearchFiltersTrait;
use App\Recruiter\Entity\Recruiter;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

class JobPostingSearchRecruiterExtension implements QueryCollectionExtensionInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        $usedTraits = class_uses($resourceClass);
        if (
            \in_array($operationName, [
                'turnover_get_recruiters_me_job_posting_search_recruiter_alerts',
                'turnover_get_recruiters_me_job_posting_search_recruiter_logs',
                'turnover_get_recruiters_me_job_posting_search_recruiter_favorites',
            ], true)
            && (\is_array($usedTraits) && \in_array(JobPostingRecruiterSearchFiltersTrait::class, $usedTraits, true))
        ) {
            $this->andWhere($queryBuilder);
        }
    }

    private function andWhere(QueryBuilder $queryBuilder): void
    {
        if (!($recruiter = $this->security->getUser()) instanceof Recruiter) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder->andWhere("$rootAlias.recruiter = :recruiter")
            ->setParameter('recruiter', $recruiter)
        ;
    }
}
