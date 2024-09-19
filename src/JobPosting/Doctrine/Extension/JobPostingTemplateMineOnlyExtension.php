<?php

namespace App\JobPosting\Doctrine\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Company\Entity\Company;
use App\JobPosting\Entity\JobPostingTemplate;
use App\Recruiter\Entity\Recruiter;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;

class JobPostingTemplateMineOnlyExtension implements QueryCollectionExtensionInterface
{
    protected Security $security;
    protected RequestStack $requestStack;

    public function __construct(RequestStack $requestStack, Security $security)
    {
        $this->security = $security;
        $this->requestStack = $requestStack;
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ): void {
        $rootAlias = $queryBuilder->getRootAliases()[0];
        if (JobPostingTemplate::class !== $resourceClass) {
            return;
        }

        /** @var Recruiter $createdBy */
        $createdBy = $this->security->getUser();
        if (!$createdBy instanceof Recruiter) {
            throw new AuthenticationException();
        }

        /** @var Company $company */
        $company = $createdBy->getCompany();

        $queryBuilder
            ->innerJoin(sprintf('%s.createdBy', $rootAlias), 'jpt_r')
            ->innerJoin('jpt_r.company', 'jpt_r_c')
            ->where($queryBuilder->expr()->orX(
                'jpt_r = :createdBy',
                'jpt_r_c = :company'
            ))
            ->setParameters([
                'createdBy' => $createdBy,
                'company' => $company,
            ])
        ;
    }
}
