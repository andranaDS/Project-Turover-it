<?php

namespace App\Company\Manager;

use App\Company\Entity\Company;
use App\Core\Util\Arrays;
use App\JobPosting\Entity\JobPosting;
use App\JobPosting\Enum\Contract;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CompanyManager
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function updateJobPostingCounts(Company $company): void
    {
        $counts = $this->em->getRepository(JobPosting::class)->countByCompanyGroupByContract($company);

        // 0 - b2c
        // 0.1 - total
        $totalCounts = $counts['total'] ?? [];
        $jobPostingsTotalCount = (int) array_sum(Arrays::subarray($totalCounts, Contract::getFreeWorkValues()));
        $jobPostingsFreeTotalCount = (int) array_sum(Arrays::subarray($totalCounts, [Contract::CONTRACTOR]));
        $jobPostingsWorkTotalCount = (int) array_sum(Arrays::subarray($totalCounts, [Contract::FIXED_TERM, Contract::INTERNSHIP, Contract::PERMANENT, Contract::APPRENTICESHIP]));

        // 0.2 - published
        $publishedCounts = $counts['published'] ?? [];
        $jobPostingsPublishedCount = (int) array_sum(Arrays::subarray($publishedCounts, Contract::getFreeWorkValues()));
        $jobPostingsFreePublishedCount = (int) array_sum(Arrays::subarray($publishedCounts, [Contract::CONTRACTOR]));
        $jobPostingsWorkPublishedCount = (int) array_sum(Arrays::subarray($publishedCounts, [Contract::FIXED_TERM, Contract::INTERNSHIP, Contract::PERMANENT, Contract::APPRENTICESHIP]));

        // 1 - b2b
        // 1.1 - total
        $jobPostingsIntercontractTotalCount = (int) array_sum(Arrays::subarray($totalCounts, Contract::getTurnoverValues()));
        // 1.2 - published
        $jobPostingsIntercontractPublishedCount = (int) array_sum(Arrays::subarray($totalCounts, Contract::getTurnoverValues()));

        if (null !== $companyData = $company->getData()) {
            $companyData->setJobPostingsTotalCount($jobPostingsTotalCount)
                ->setJobPostingsFreeTotalCount($jobPostingsFreeTotalCount)
                ->setJobPostingsWorkTotalCount($jobPostingsWorkTotalCount)
                ->setJobPostingsPublishedCount($jobPostingsPublishedCount)
                ->setJobPostingsFreePublishedCount($jobPostingsFreePublishedCount)
                ->setJobPostingsWorkPublishedCount($jobPostingsWorkPublishedCount)
                ->setJobPostingsIntercontractPublishedCount($jobPostingsIntercontractPublishedCount)
                ->setJobPostingsIntercontractTotalCount($jobPostingsIntercontractTotalCount)
            ;
        }
    }

    public function updateUsersCount(Company $company): void
    {
        $counts = $this->em->getRepository(User::class)->countByCompany($company);

        $totalCount = (int) array_sum(array_column($counts, 'count'));
        $index = array_search(true, array_column($counts, 'visible'), true);
        $visibleCount = false !== $index ? (int) $counts[$index]['count'] : 0;

        if (null !== $companyData = $company->getData()) {
            $companyData->setUsersCount($totalCount);
            $companyData->setUsersVisibleCount($visibleCount);
        }
    }
}
