<?php

namespace App\JobPosting\Doctrine\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\JobPosting\Entity\JobPosting;
use App\Recruiter\Entity\Recruiter;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class JobPostingCompaniesSlugExtension implements QueryCollectionExtensionInterface
{
    private Security $security;
    private RequestStack $requestStack;

    public function __construct(Security $security, RequestStack $requestStack)
    {
        $this->security = $security;
        $this->requestStack = $requestStack;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        if (JobPosting::class !== $resourceClass) {
            return;
        }

        if ('freework_get_companies_slug_job_postings' === $operationName) {
            $this->andWhere($queryBuilder, $this->fetchCompanySlugFromRequest());
        } elseif ('turnover_get_companies_mine_job_postings' === $operationName) {
            $this->andWhere($queryBuilder, $this->fetchCompanySlugFromLoggedUser());
        }
    }

    private function andWhere(QueryBuilder $queryBuilder, ?string $companySlug): void
    {
        if (null === $companySlug) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->join(sprintf('%s.company', $rootAlias), 'c')
            ->where('c.slug = :slug')
            ->setParameter('slug', $companySlug)
        ;
    }

    private function fetchCompanySlugFromRequest(): ?string
    {
        if (null === $request = $this->requestStack->getMainRequest()) {
            return null;
        }

        return $request->attributes->get('slug');
    }

    private function fetchCompanySlugFromLoggedUser(): ?string
    {
        $recruiter = $this->security->getUser();
        if (!$recruiter instanceof Recruiter || null === $company = $recruiter->getCompany()) {
            return null;
        }

        return $company->getSlug();
    }
}
